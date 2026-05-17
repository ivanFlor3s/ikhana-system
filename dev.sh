#!/bin/bash
set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
CYAN='\033[0;36m'
NC='\033[0m'

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
FRONTEND_PID=""

cleanup() {
    echo ""
    echo -e "${YELLOW}Shutting down...${NC}"
    if [ -n "$FRONTEND_PID" ] && kill -0 "$FRONTEND_PID" 2>/dev/null; then
        kill "$FRONTEND_PID" 2>/dev/null
        echo -e "${GREEN}Frontend stopped.${NC}"
    fi
    cd "$SCRIPT_DIR/backend"
    docker compose down 2>/dev/null || docker-compose down 2>/dev/null
    echo -e "${GREEN}Backend + DB stopped.${NC}"
    echo -e "${GREEN}Done.${NC}"
    exit 0
}
trap cleanup SIGINT SIGTERM

echo -e "${CYAN}╔══════════════════════════════════╗${NC}"
echo -e "${CYAN}║   Ikhana Dev Environment Setup   ║${NC}"
echo -e "${CYAN}╚══════════════════════════════════╝${NC}"
echo ""

# ── 1. Prerequisites ──
echo -e "${YELLOW}[1/6]${NC} Checking prerequisites..."

if ! command -v docker &>/dev/null; then
    echo -e "${RED}Docker is not installed. Please install Docker first.${NC}"
    exit 1
fi

if docker compose version &>/dev/null; then
    DOCKER_COMPOSE="docker compose"
elif docker-compose version &>/dev/null; then
    DOCKER_COMPOSE="docker-compose"
else
    echo -e "${RED}Docker Compose is not available.${NC}"
    exit 1
fi

if ! command -v node &>/dev/null; then
    echo -e "${RED}Node.js is not installed. Please install Node.js first.${NC}"
    exit 1
fi

if ! command -v npm &>/dev/null; then
    echo -e "${RED}npm is not installed. Please install npm first.${NC}"
    exit 1
fi

echo -e "  Docker        ${GREEN}✓${NC}"
echo -e "  Docker Compose ${GREEN}✓${NC} ($DOCKER_COMPOSE)"
echo -e "  Node.js       ${GREEN}✓${NC} ($(node -v))"
echo -e "  npm           ${GREEN}✓${NC} ($(npm -v))"
echo ""

# ── 2. Backend .env ──
echo -e "${YELLOW}[2/6]${NC} Setting up backend environment..."

cd "$SCRIPT_DIR/backend"

if [ ! -f src/.env ]; then
    cp src/.env.example src/.env
    echo -e "  ${GREEN}✓${NC} Created src/.env from .env.example"
else
    echo -e "  ${GREEN}✓${NC} src/.env already exists"
fi
echo ""

# ── 3. Start Docker services ──
echo -e "${YELLOW}[3/6]${NC} Starting database + backend containers..."

cd "$SCRIPT_DIR/backend"

BUILD_FLAG=""
if [ "$1" = "--build" ]; then
    BUILD_FLAG="--build"
fi

$DOCKER_COMPOSE up -d $BUILD_FLAG

echo -e "  ${GREEN}✓${NC} Containers started (ikhana_db, ikhana_app, ikhana_nginx)"
echo ""

# ── 4. Wait for DB to be healthy ──
echo -e "${YELLOW}[4/6]${NC} Waiting for database to be ready..."
until $DOCKER_COMPOSE exec -T db mysqladmin ping -h localhost --silent 2>/dev/null; do
    sleep 2
done
echo -e "  ${GREEN}✓${NC} Database is ready"
echo ""

# ── 5. Run backend setup ──
echo -e "${YELLOW}[5/6]${NC} Running backend setup (composer, migrate, seed, docs)..."
echo "────────────────────────────────────"
$DOCKER_COMPOSE exec -T app bash /usr/local/bin/setup.sh
echo "────────────────────────────────────"
echo -e "  ${GREEN}✓${NC} Backend setup complete"
echo ""

# ── 6. Frontend ──
echo -e "${YELLOW}[6/6]${NC} Installing frontend dependencies..."

cd "$SCRIPT_DIR/frontend"
npm install --silent

echo -e "  ${GREEN}✓${NC} Frontend dependencies installed"
echo ""

echo -e "${YELLOW}Starting frontend dev server...${NC}"
npm start &
FRONTEND_PID=$!

sleep 3
if ! kill -0 "$FRONTEND_PID" 2>/dev/null; then
    echo -e "${RED}Frontend failed to start. Check for errors above.${NC}"
    cleanup
fi

echo ""
echo -e "${GREEN}╔══════════════════════════════════╗${NC}"
echo -e "${GREEN}║   Ikhana is running!             ║${NC}"
echo -e "${GREEN}╠══════════════════════════════════╣${NC}"
echo -e "${GREEN}║${NC}  Backend API  ${CYAN}http://localhost:8000/api${NC}"
echo -e "${GREEN}║${NC}  API Docs     ${CYAN}http://localhost:8000/docs${NC}"
echo -e "${GREEN}║${NC}  Frontend     ${CYAN}http://localhost:4200${NC}"
echo -e "${GREEN}╚══════════════════════════════════╝${NC}"
echo ""
echo -e "Press ${YELLOW}Ctrl+C${NC} to stop all services"
echo ""

wait $FRONTEND_PID
