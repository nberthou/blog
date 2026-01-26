import { config } from '@vue/test-utils';
import { vi } from 'vitest';

// Mock Inertia's usePage
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual('@inertiajs/vue3');
    return {
        ...actual,
        usePage: vi.fn(() => ({
            props: {
                auth: {
                    user: null,
                },
            },
            url: '/',
        })),
        useForm: vi.fn((initialData) => ({
            ...initialData,
            processing: false,
            errors: {},
            post: vi.fn(),
            put: vi.fn(),
            delete: vi.fn(),
            reset: vi.fn(),
        })),
        Link: {
            name: 'Link',
            template: '<a><slot /></a>',
            props: ['href'],
        },
        Head: {
            name: 'Head',
            template: '<div></div>',
            props: ['title'],
        },
    };
});

// Mock route helpers
vi.mock('@/routes/categories', () => ({
    index: vi.fn(() => ({ url: '/categories' })),
    show: vi.fn((category) => ({
        url: `/categories/${category.slug || category}`,
    })),
    create: vi.fn(() => ({ url: '/categories-create' })),
    store: vi.fn(() => ({ url: '/categories' })),
    edit: vi.fn((category) => ({
        url: `/categories/${category.slug || category}/edit`,
    })),
    update: vi.fn((category) => ({
        url: `/categories/${category.slug || category}`,
    })),
    destroy: vi.fn((category) => ({
        url: `/categories/${category.slug || category}`,
    })),
}));

vi.mock('@/routes/posts', () => ({
    index: vi.fn(() => ({ url: '/posts' })),
    show: vi.fn((post) => ({ url: `/posts/${post.slug || post}` })),
    create: vi.fn(() => ({ url: '/posts-create' })),
}));

// Global stubs for common components
config.global.stubs = {
    Head: true,
    teleport: true,
};
