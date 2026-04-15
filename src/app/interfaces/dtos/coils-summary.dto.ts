/**
 * page   integer  optional    
Número de página. Example: 1

per_page   integer  optional    
Items por página (default: 15, max: 100). Example: 20

search   string  optional    
Filtrar por nombre del proveedor (fantasy_name o business_name). Example: Rio Batel

sort_by   string  optional    
Columna para ordenar: provider_name, coils_count, last_movement_in, last_movement_out. Example: coils_count

sort_dir   string  optional    
Dirección: asc o desc (default: asc). Example: desc
 */

export interface CoilsSummaryDto {
    page: number;
    per_page: number;
    search?: string;
    sort_by?: string;
    sort_dir?: string;
}