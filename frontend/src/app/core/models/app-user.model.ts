export interface AppUser {
    id: number
    name: string
    email: string
    role: AppRole
}

export type AppRole = 'Admin' | 'Administracion' | 'Operador' | 'Consultor';