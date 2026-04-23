export interface Event {
    id: number;
    title: string;
    description: string | null;
    picture_url: string;
    starts_at: Date;
    ends_at: Date;
    deadline: Date | null;
    created_at: Date;
    updated_at: Date;
}

export interface User {
    id: number;
    email: string;
    password:string
    first_name: string,
    last_name: string,
    avatar_url: string,
    created_at: Date;
    updated_at: Date;
}

export interface Membership {
    id: number;
    user_id: number;
    event_id: number;
    role_id: number;
    group_id: number;
    created_at: Date;
    updated_at: Date;
}
export interface Group {
    id: number;
    name: string;
    description: string | null;
    picture_url: string;
    created_at: Date;
    updated_at: Date;
}

export interface Attendance {
    id: number;
    event_id: number;
    user_id: number;
    attends: boolean;
    created_at: Date;
    updated_at: Date;
}

export interface Payment {
    id: number;
    event_id: number;
    amount_paid: number;
    user_id: number;
    created_at: Date;
    updated_at: Date;
}

export interface setting {
    option_id: number;
    setting_id: number;
    created_at: Date;
    updated_at: Date;
}
