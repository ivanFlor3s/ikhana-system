using System;
using Microsoft.EntityFrameworkCore.Metadata;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace Ikhana.Infrastructure.Migrations
{
    /// <inheritdoc />
    public partial class Wave456_Entities : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateTable(
                name: "ProviderInventories",
                columns: table => new
                {
                    Id = table.Column<long>(type: "bigint", nullable: false)
                        .Annotation("MySql:ValueGenerationStrategy", MySqlValueGenerationStrategy.IdentityColumn),
                    ProviderId = table.Column<long>(type: "bigint", nullable: false),
                    CoilsCount = table.Column<int>(type: "int", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ProviderInventories", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ProviderInventories_Providers_ProviderId",
                        column: x => x.ProviderId,
                        principalTable: "Providers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                })
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.CreateTable(
                name: "RawMaterialEntries",
                columns: table => new
                {
                    Id = table.Column<long>(type: "bigint", nullable: false)
                        .Annotation("MySql:ValueGenerationStrategy", MySqlValueGenerationStrategy.IdentityColumn),
                    RawMaterialTypeId = table.Column<long>(type: "bigint", nullable: false),
                    ProviderId = table.Column<long>(type: "bigint", nullable: false),
                    RawMaterialCharacteristicId = table.Column<long>(type: "bigint", nullable: false),
                    EntryNumber = table.Column<int>(type: "int", nullable: true),
                    Remito = table.Column<string>(type: "varchar(50)", maxLength: 50, nullable: true)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    Batch = table.Column<int>(type: "int", nullable: true),
                    EntryDate = table.Column<DateOnly>(type: "date", nullable: false),
                    QuantityKg = table.Column<decimal>(type: "decimal(12,3)", nullable: true),
                    CoilsCount = table.Column<int>(type: "int", nullable: true),
                    Status = table.Column<int>(type: "int", nullable: false),
                    Observations = table.Column<string>(type: "text", nullable: true)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    DeletedAt = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_RawMaterialEntries", x => x.Id);
                    table.ForeignKey(
                        name: "FK_RawMaterialEntries_Providers_ProviderId",
                        column: x => x.ProviderId,
                        principalTable: "Providers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                    table.ForeignKey(
                        name: "FK_RawMaterialEntries_RawMaterialCharacteristics_RawMaterialCha~",
                        column: x => x.RawMaterialCharacteristicId,
                        principalTable: "RawMaterialCharacteristics",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                    table.ForeignKey(
                        name: "FK_RawMaterialEntries_RawMaterialTypes_RawMaterialTypeId",
                        column: x => x.RawMaterialTypeId,
                        principalTable: "RawMaterialTypes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                })
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.CreateTable(
                name: "MaterialTests",
                columns: table => new
                {
                    Id = table.Column<long>(type: "bigint", nullable: false)
                        .Annotation("MySql:ValueGenerationStrategy", MySqlValueGenerationStrategy.IdentityColumn),
                    RawMaterialEntryId = table.Column<long>(type: "bigint", nullable: false),
                    TestDate = table.Column<DateOnly>(type: "date", nullable: false),
                    ResistanceOhmKm = table.Column<decimal>(type: "decimal(10,2)", nullable: false),
                    ElongationPct = table.Column<decimal>(type: "decimal(5,2)", nullable: true),
                    CheckWinding = table.Column<bool>(type: "tinyint(1)", nullable: true),
                    CheckCleanliness = table.Column<bool>(type: "tinyint(1)", nullable: true),
                    CheckPackaging = table.Column<bool>(type: "tinyint(1)", nullable: true),
                    CheckIdentification = table.Column<bool>(type: "tinyint(1)", nullable: true),
                    Result = table.Column<string>(type: "varchar(10)", maxLength: 10, nullable: false)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    ConductedBy = table.Column<string>(type: "varchar(120)", maxLength: 120, nullable: true)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    ApprovedBy = table.Column<string>(type: "varchar(120)", maxLength: 120, nullable: true)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    DeletedAt = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_MaterialTests", x => x.Id);
                    table.ForeignKey(
                        name: "FK_MaterialTests_RawMaterialEntries_RawMaterialEntryId",
                        column: x => x.RawMaterialEntryId,
                        principalTable: "RawMaterialEntries",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                })
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.CreateTable(
                name: "ProviderCoilMovements",
                columns: table => new
                {
                    Id = table.Column<long>(type: "bigint", nullable: false)
                        .Annotation("MySql:ValueGenerationStrategy", MySqlValueGenerationStrategy.IdentityColumn),
                    ProviderId = table.Column<long>(type: "bigint", nullable: false),
                    RawMaterialEntryId = table.Column<long>(type: "bigint", nullable: true),
                    Type = table.Column<string>(type: "varchar(20)", maxLength: 20, nullable: false)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    CoilsReceived = table.Column<int>(type: "int", nullable: false),
                    CoilsReturned = table.Column<int>(type: "int", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ProviderCoilMovements", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ProviderCoilMovements_Providers_ProviderId",
                        column: x => x.ProviderId,
                        principalTable: "Providers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_ProviderCoilMovements_RawMaterialEntries_RawMaterialEntryId",
                        column: x => x.RawMaterialEntryId,
                        principalTable: "RawMaterialEntries",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.SetNull);
                })
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.CreateIndex(
                name: "IX_MaterialTests_RawMaterialEntryId",
                table: "MaterialTests",
                column: "RawMaterialEntryId",
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_ProviderCoilMovements_ProviderId",
                table: "ProviderCoilMovements",
                column: "ProviderId");

            migrationBuilder.CreateIndex(
                name: "IX_ProviderCoilMovements_RawMaterialEntryId",
                table: "ProviderCoilMovements",
                column: "RawMaterialEntryId");

            migrationBuilder.CreateIndex(
                name: "IX_ProviderInventories_ProviderId",
                table: "ProviderInventories",
                column: "ProviderId",
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_RawMaterialEntries_ProviderId",
                table: "RawMaterialEntries",
                column: "ProviderId");

            migrationBuilder.CreateIndex(
                name: "IX_RawMaterialEntries_RawMaterialCharacteristicId",
                table: "RawMaterialEntries",
                column: "RawMaterialCharacteristicId");

            migrationBuilder.CreateIndex(
                name: "IX_RawMaterialEntries_RawMaterialTypeId_EntryDate",
                table: "RawMaterialEntries",
                columns: new[] { "RawMaterialTypeId", "EntryDate" });
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "MaterialTests");

            migrationBuilder.DropTable(
                name: "ProviderCoilMovements");

            migrationBuilder.DropTable(
                name: "ProviderInventories");

            migrationBuilder.DropTable(
                name: "RawMaterialEntries");
        }
    }
}
