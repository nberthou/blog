export interface Category {
    id: number;
    name: string;
    slug: string;
    description?: string | null;
    posts_count?: number;
    posts?: Post[];
}

export interface Author {
    id: number;
    name: string;
}

export interface Post {
    id: number;
    user_id: number;
    title: string;
    slug: string;
    excerpt: string;
    content: string;
    status: 'draft' | 'published' | 'scheduled' | 'archived';
    view_count: number;
    published_at: string | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    author?: Author;
    categories?: Category[];
    featured_image_url?: string | null;
    is_published?: boolean;
}

export interface PostStatus {
    value: string;
    label: string;
    color?: string;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    first_page_url: string;
    from: number | null;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
}
