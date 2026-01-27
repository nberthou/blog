import type { Category, Comment, Post } from '@/types/post';

export const createMockCategory = (
    overrides: Partial<Category> = {},
): Category => ({
    id: 1,
    name: 'Technologie',
    slug: 'technologie',
    description: 'Articles sur la technologie',
    posts_count: 5,
    ...overrides,
});

export const createMockPost = (overrides: Partial<Post> = {}): Post => ({
    id: 1,
    user_id: 1,
    title: 'Mon article',
    slug: 'mon-article',
    excerpt: 'Un extrait',
    content: '<p>Le contenu</p>',
    status: 'published',
    view_count: 10,
    published_at: '2024-01-15T10:00:00Z',
    created_at: '2024-01-15T10:00:00Z',
    updated_at: '2024-01-15T10:00:00Z',
    deleted_at: null,
    ...overrides,
});

export const createMockPaginatedData = <T>(
    data: T[],
    overrides: Record<string, unknown> = {},
) => ({
    data,
    current_page: 1,
    first_page_url: '?page=1',
    from: data.length > 0 ? 1 : null,
    last_page: 1,
    last_page_url: '?page=1',
    links: [
        { url: null, label: '&laquo; Précédent', active: false },
        { url: '?page=1', label: '1', active: true },
        { url: null, label: 'Suivant &raquo;', active: false },
    ],
    next_page_url: null,
    path: '/categories',
    per_page: 15,
    prev_page_url: null,
    to: data.length > 0 ? data.length : null,
    total: data.length,
    ...overrides,
});

export const createMockComment = (
    overrides: Partial<Comment> = {},
): Comment => ({
    id: 1,
    post_id: 1,
    user_id: 1,
    parent_id: null,
    content: 'Ceci est un commentaire de test.',
    created_at: '2024-01-15T10:00:00Z',
    updated_at: '2024-01-15T10:00:00Z',
    author: { id: 1, name: 'Jean Dupont' },
    replies: [],
    ...overrides,
});
