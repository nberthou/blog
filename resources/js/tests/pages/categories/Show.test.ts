import Show from '@/pages/categories/Show.vue';
import { usePage } from '@inertiajs/vue3';
import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { createMockCategory, createMockPost } from '../../helpers';

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

describe('Categories Show Page', () => {
    const mockPosts = [
        createMockPost({ id: 1, title: 'Article 1', slug: 'article-1' }),
        createMockPost({ id: 2, title: 'Article 2', slug: 'article-2' }),
    ];

    const mockCategory = createMockCategory({
        id: 1,
        name: 'Technologie',
        slug: 'technologie',
        description: 'Articles sur la technologie',
        posts_count: 2,
        posts: mockPosts,
    });

    beforeEach(() => {
        vi.mocked(usePage).mockReturnValue({
            props: {
                auth: { user: null },
            },
            url: '/categories/technologie',
        } as ReturnType<typeof usePage>);
    });

    it('renders the category name', () => {
        const wrapper = mount(Show, {
            props: {
                category: mockCategory,
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    PostCard: true,
                    ConfirmDialog: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Technologie');
    });

    it('renders the category description', () => {
        const wrapper = mount(Show, {
            props: {
                category: mockCategory,
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    PostCard: true,
                    ConfirmDialog: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Articles sur la technologie');
    });

    it('renders the post count', () => {
        const wrapper = mount(Show, {
            props: {
                category: mockCategory,
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    PostCard: true,
                    ConfirmDialog: true,
                },
            },
        });

        expect(wrapper.text()).toContain('2 articles');
    });

    it('renders singular form for one article', () => {
        const singlePostCategory = createMockCategory({
            ...mockCategory,
            posts_count: 1,
            posts: [mockPosts[0]],
        });

        const wrapper = mount(Show, {
            props: {
                category: singlePostCategory,
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    PostCard: true,
                    ConfirmDialog: true,
                },
            },
        });

        expect(wrapper.text()).toContain('1 article');
        expect(wrapper.text()).not.toContain('1 articles');
    });

    it('renders back link to categories index', () => {
        const wrapper = mount(Show, {
            props: {
                category: mockCategory,
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    PostCard: true,
                    ConfirmDialog: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Retour aux catégories');
    });

    it('hides admin actions for non-admin users', () => {
        const wrapper = mount(Show, {
            props: {
                category: mockCategory,
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    PostCard: true,
                    ConfirmDialog: true,
                },
            },
        });

        expect(wrapper.text()).not.toContain('Modifier');
        expect(wrapper.text()).not.toContain('Supprimer');
    });

    it('shows admin actions for admin users', () => {
        vi.mocked(usePage).mockReturnValue({
            props: {
                auth: { user: { id: 1, name: 'Admin' } },
            },
            url: '/categories/technologie',
        } as ReturnType<typeof usePage>);

        const wrapper = mount(Show, {
            props: {
                category: mockCategory,
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    PostCard: true,
                    ConfirmDialog: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Modifier');
        expect(wrapper.text()).toContain('Supprimer');
    });

    it('shows empty state when no posts', () => {
        const emptyCategory = createMockCategory({
            ...mockCategory,
            posts_count: 0,
            posts: [],
        });

        const wrapper = mount(Show, {
            props: {
                category: emptyCategory,
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    PostCard: true,
                    ConfirmDialog: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Aucun article dans cette catégorie');
    });

    it('renders PostCard for each post', () => {
        const wrapper = mount(Show, {
            props: {
                category: mockCategory,
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    PostCard: {
                        template:
                            '<div class="post-card">{{ post.title }}</div>',
                        props: ['post', 'showStatus', 'showViewCount'],
                    },
                    ConfirmDialog: true,
                },
            },
        });

        const postCards = wrapper.findAll('.post-card');
        expect(postCards).toHaveLength(2);
    });

    it('does not render description when not provided', () => {
        const categoryWithoutDesc = createMockCategory({
            ...mockCategory,
            description: null,
        });

        const wrapper = mount(Show, {
            props: {
                category: categoryWithoutDesc,
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                    PostCard: true,
                    ConfirmDialog: true,
                },
            },
        });

        // Should not crash and should render category name
        expect(wrapper.text()).toContain('Technologie');
    });
});
