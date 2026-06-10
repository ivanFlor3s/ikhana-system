using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace Ikhana.Infrastructure.Migrations
{
    /// <inheritdoc />
    public partial class Wave7_ProviderPhoneEmail : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<string>(
                name: "Email",
                table: "Providers",
                type: "varchar(255)",
                maxLength: 255,
                nullable: true)
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.AddColumn<string>(
                name: "Phone",
                table: "Providers",
                type: "varchar(50)",
                maxLength: 50,
                nullable: true)
                .Annotation("MySql:CharSet", "utf8mb4");
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "Email",
                table: "Providers");

            migrationBuilder.DropColumn(
                name: "Phone",
                table: "Providers");
        }
    }
}
