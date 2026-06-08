using System;
using Microsoft.EntityFrameworkCore.Metadata;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace Ikhana.Infrastructure.Migrations
{
    /// <inheritdoc />
    public partial class Wave3_RawMaterials : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateTable(
                name: "RawMaterialTypes",
                columns: table => new
                {
                    Id = table.Column<long>(type: "bigint", nullable: false)
                        .Annotation("MySql:ValueGenerationStrategy", MySqlValueGenerationStrategy.IdentityColumn),
                    Name = table.Column<string>(type: "varchar(50)", maxLength: 50, nullable: false)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    DeletedAt = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_RawMaterialTypes", x => x.Id);
                })
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.CreateTable(
                name: "RawMaterialCharacteristics",
                columns: table => new
                {
                    Id = table.Column<long>(type: "bigint", nullable: false)
                        .Annotation("MySql:ValueGenerationStrategy", MySqlValueGenerationStrategy.IdentityColumn),
                    RawMaterialTypeId = table.Column<long>(type: "bigint", nullable: false),
                    Name = table.Column<string>(type: "varchar(50)", maxLength: 50, nullable: false)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    DecimalValue = table.Column<decimal>(type: "decimal(10,3)", nullable: true),
                    TextValue = table.Column<string>(type: "varchar(100)", maxLength: 100, nullable: true)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    Unit = table.Column<string>(type: "varchar(20)", maxLength: 20, nullable: true)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    DeletedAt = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_RawMaterialCharacteristics", x => x.Id);
                    table.ForeignKey(
                        name: "FK_RawMaterialCharacteristics_RawMaterialTypes_RawMaterialTypeId",
                        column: x => x.RawMaterialTypeId,
                        principalTable: "RawMaterialTypes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                })
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.CreateTable(
                name: "IramOhmMaxResistances",
                columns: table => new
                {
                    Id = table.Column<long>(type: "bigint", nullable: false)
                        .Annotation("MySql:ValueGenerationStrategy", MySqlValueGenerationStrategy.IdentityColumn),
                    RawMaterialCharacteristicId = table.Column<long>(type: "bigint", nullable: false),
                    MaxResistanceOhmKm = table.Column<decimal>(type: "decimal(10,2)", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_IramOhmMaxResistances", x => x.Id);
                    table.ForeignKey(
                        name: "FK_IramOhmMaxResistances_RawMaterialCharacteristics_RawMaterial~",
                        column: x => x.RawMaterialCharacteristicId,
                        principalTable: "RawMaterialCharacteristics",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                })
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.CreateIndex(
                name: "IX_IramOhmMaxResistances_RawMaterialCharacteristicId",
                table: "IramOhmMaxResistances",
                column: "RawMaterialCharacteristicId",
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_RawMaterialCharacteristics_RawMaterialTypeId_Name_DecimalVal~",
                table: "RawMaterialCharacteristics",
                columns: new[] { "RawMaterialTypeId", "Name", "DecimalValue" },
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_RawMaterialCharacteristics_RawMaterialTypeId_Name_TextValue",
                table: "RawMaterialCharacteristics",
                columns: new[] { "RawMaterialTypeId", "Name", "TextValue" },
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_RawMaterialTypes_Name",
                table: "RawMaterialTypes",
                column: "Name",
                unique: true);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "IramOhmMaxResistances");

            migrationBuilder.DropTable(
                name: "RawMaterialCharacteristics");

            migrationBuilder.DropTable(
                name: "RawMaterialTypes");
        }
    }
}
