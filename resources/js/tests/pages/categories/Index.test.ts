import Index from '@/pages/categories/Index.vue';
import { usePage } from '@inertiajs/vue3';
import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { createMockCategory, createMockPaginatedData } from '../../helpers';

vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual('@inertiajs/vue3');
    return {
        ...actual,
        usePage: vi.fn(),
        useForm: vi.fn(() => ({
            processing: false,
            errors: {},
            delete: vi.fn(),
        })),
        Link: {
            name: 'Link',
            template: '<a :href="href"><slot /></a>',
            props: ['href'],
        },
        Head: {
            name: 'Head',
            template: '<div></div>',
            props: ['title'],
        },
    };
});

describe('Categories Index Page', () => {
    const mockCategories = [
        createMockCategory({
            id: 1,
            name: 'Technologie',
            slug: 'technologie',
            posts_count: 5,
        }),
        createMockCategory({
            id: 2,
            name: 'Voyage',
            slug: 'voyage',
            posts_count: 3,
        }),
        createMockCategory({
            id: 3,
            name: 'Cuisine',
            slug: 'cuisine',
            posts_count: 0,
        }),
    ];

    beforeEach(() => {
        vi.mocked(usePage).mockReturnValue({
            props: {
                auth: { user: null },
            },
            url: '/categories',
        } as ReturnType<typeof usePage>);
    });

    it('renders the page title', () => {
        const wrapper = mount(Index, {
            props: {
                categories: createMockPaginatedData(mockCategories),
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    Pagination: true,
                    ConfirmDialog: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Catégories');
    });

    it('displays all categories in the table', () => {
        const wrapper = mount(Index, {
            props: {
                categories: createMockPaginatedData(mockCategories),
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    Pagination: true,
                    ConfirmDialog: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Technologie');
        expect(wrapper.text()).toContain('Voyage');
        expect(wrapper.text()).toContain('Cuisine');
    });

    it('displays post count for each category', () => {
        const wrapper = mount(Index, {
            props: {
                categories: createMockPaginatedData(mockCategories),
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    Pagination: true,
                    ConfirmDialog: true,
                },
            },
        });

        expect(wrapper.text()).toContain('5');
        expect(wrapper.text()).toContain('3');
        expect(wrapper.text()).toContain('0');
    });

    it('shows empty state when no categories', () => {
        const wrapper = mount(Index, {
            props: {
                categories: createMockPaginatedData([]),
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    Pagination: true,
                    ConfirmDialog: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Aucune catégorie pour le moment');
    });

    it('hides create button for non-authenticated users', () => {
        const wrapper = mount(Index, {
            props: {
                categories: createMockPaginatedData(mockCategories),
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    Pagination: true,
                    ConfirmDialog: true,
                },
            },
        });

        expect(wrapper.text()).not.toContain('Nouvelle catégorie');
    });

    it('hides edit/delete buttons for non-admin users', () => {
        vi.mocked(usePage).mockReturnValue({
            props: {
                auth: { user: { id: 2, name: 'User' } },
            },
            url: '/categories',
        } as ReturnType<typeof usePage>);

        const wrapper = mount(Index, {
            props: {
                categories: createMockPaginatedData(mockCategories),
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    Pagination: true,
                    ConfirmDialog: true,
                },
            },
        });

        // Action column should not be visible for non-admin
        expect(wrapper.text()).not.toContain('Actions');
    });

    it('shows create button for admin users', () => {
        vi.mocked(usePage).mockReturnValue({
            props: {
                auth: { user: { id: 1, name: 'Admin' } },
            },
            url: '/categories',
        } as ReturnType<typeof usePage>);

        const wrapper = mount(Index, {
            props: {
                categories: createMockPaginatedData(mockCategories),
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    Pagination: true,
                    ConfirmDialog: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Nouvelle catégorie');
    });

    it('shows action buttons for admin users', () => {
        vi.mocked(usePage).mockReturnValue({
            props: {
                auth: { user: { id: 1, name: 'Admin' } },
            },
            url: '/categories',
        } as ReturnType<typeof usePage>);

        const wrapper = mount(Index, {
            props: {
                categories: createMockPaginatedData(mockCategories),
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    Pagination: true,
                    ConfirmDialog: true,
                },
            },
        });

        // Action column should be visible for admin
        expect(wrapper.text()).toContain('Actions');
    });

    it('displays category descriptions', () => {
        const wrapper = mount(Index, {
            props: {
                categories: createMockPaginatedData(mockCategories),
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    Pagination: true,
                    ConfirmDialog: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Articles sur la technologie');
    });

    it('shows "Aucune description" for categories without description', () => {
        const categoriesWithoutDesc = [
            createMockCategory({ id: 1, name: 'Test', description: null }),
        ];

        const wrapper = mount(Index, {
            props: {
                categories: createMockPaginatedData(categoriesWithoutDesc),
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    Pagination: true,
                    ConfirmDialog: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Aucune description');
    });
});
