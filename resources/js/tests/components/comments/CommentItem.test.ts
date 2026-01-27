import CommentItem from '@/components/comments/CommentItem.vue';
import { usePage } from '@inertiajs/vue3';
import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { createMockComment } from '../../helpers';

vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual('@inertiajs/vue3');
    return {
        ...actual,
        usePage: vi.fn(),
        useForm: vi.fn(() => ({
            processing: false,
            errors: {},
            content: 'Test content',
            put: vi.fn(),
            delete: vi.fn(),
            reset: vi.fn(),
        })),
    };
});

describe('CommentItem', () => {
    const mockComment = createMockComment({
        id: 1,
        user_id: 2,
        content: 'Ceci est un commentaire de test.',
        author: { id: 2, name: 'Jean Dupont' },
        created_at: '2024-01-15T10:00:00Z',
        updated_at: '2024-01-15T10:00:00Z',
    });

    beforeEach(() => {
        vi.mocked(usePage).mockReturnValue({
            props: {
                auth: { user: null },
            },
            url: '/posts/test-post',
        } as ReturnType<typeof usePage>);
    });

    it('displays comment content', () => {
        const wrapper = mount(CommentItem, {
            props: {
                comment: mockComment,
                postSlug: 'test-post',
            },
            global: {
                stubs: {
                    Avatar: {
                        template: '<div class="avatar-stub"><slot /></div>',
                    },
                    AvatarFallback: { template: '<span><slot /></span>' },
                    Button: { template: '<button><slot /></button>' },
                    Textarea: true,
                    ConfirmDialog: true,
                    CommentForm: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Ceci est un commentaire de test.');
    });

    it('displays author name', () => {
        const wrapper = mount(CommentItem, {
            props: {
                comment: mockComment,
                postSlug: 'test-post',
            },
            global: {
                stubs: {
                    Avatar: {
                        template: '<div class="avatar-stub"><slot /></div>',
                    },
                    AvatarFallback: { template: '<span><slot /></span>' },
                    Button: { template: '<button><slot /></button>' },
                    Textarea: true,
                    ConfirmDialog: true,
                    CommentForm: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Jean Dupont');
    });

    it('displays author initials in avatar', () => {
        const wrapper = mount(CommentItem, {
            props: {
                comment: mockComment,
                postSlug: 'test-post',
            },
            global: {
                stubs: {
                    Avatar: {
                        template: '<div class="avatar-stub"><slot /></div>',
                    },
                    AvatarFallback: {
                        template:
                            '<span class="avatar-fallback"><slot /></span>',
                    },
                    Button: { template: '<button><slot /></button>' },
                    Textarea: true,
                    ConfirmDialog: true,
                    CommentForm: true,
                },
            },
        });

        expect(wrapper.find('.avatar-fallback').text()).toBe('JD');
    });

    it('hides edit/delete buttons for non-authenticated users', () => {
        const wrapper = mount(CommentItem, {
            props: {
                comment: mockComment,
                postSlug: 'test-post',
            },
            global: {
                stubs: {
                    Avatar: {
                        template: '<div class="avatar-stub"><slot /></div>',
                    },
                    AvatarFallback: { template: '<span><slot /></span>' },
                    Button: { template: '<button><slot /></button>' },
                    Textarea: true,
                    ConfirmDialog: true,
                    CommentForm: true,
                },
            },
        });

        expect(wrapper.text()).not.toContain('Modifier');
        expect(wrapper.text()).not.toContain('Supprimer');
    });

    it('hides reply button for non-authenticated users', () => {
        const wrapper = mount(CommentItem, {
            props: {
                comment: mockComment,
                postSlug: 'test-post',
            },
            global: {
                stubs: {
                    Avatar: {
                        template: '<div class="avatar-stub"><slot /></div>',
                    },
                    AvatarFallback: { template: '<span><slot /></span>' },
                    Button: { template: '<button><slot /></button>' },
                    Textarea: true,
                    ConfirmDialog: true,
                    CommentForm: true,
                },
            },
        });

        expect(wrapper.text()).not.toContain('Repondre');
    });

    it('shows edit/delete buttons for comment author', () => {
        vi.mocked(usePage).mockReturnValue({
            props: {
                auth: { user: { id: 2, name: 'Jean Dupont' } },
            },
            url: '/posts/test-post',
        } as ReturnType<typeof usePage>);

        const wrapper = mount(CommentItem, {
            props: {
                comment: mockComment,
                postSlug: 'test-post',
            },
            global: {
                stubs: {
                    Avatar: {
                        template: '<div class="avatar-stub"><slot /></div>',
                    },
                    AvatarFallback: { template: '<span><slot /></span>' },
                    Button: { template: '<button><slot /></button>' },
                    Textarea: true,
                    ConfirmDialog: true,
                    CommentForm: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Modifier');
        expect(wrapper.text()).toContain('Supprimer');
    });

    it('shows edit/delete buttons for admin', () => {
        vi.mocked(usePage).mockReturnValue({
            props: {
                auth: { user: { id: 1, name: 'Admin' } },
            },
            url: '/posts/test-post',
        } as ReturnType<typeof usePage>);

        const wrapper = mount(CommentItem, {
            props: {
                comment: mockComment,
                postSlug: 'test-post',
            },
            global: {
                stubs: {
                    Avatar: {
                        template: '<div class="avatar-stub"><slot /></div>',
                    },
                    AvatarFallback: { template: '<span><slot /></span>' },
                    Button: { template: '<button><slot /></button>' },
                    Textarea: true,
                    ConfirmDialog: true,
                    CommentForm: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Modifier');
        expect(wrapper.text()).toContain('Supprimer');
    });

    it('shows reply button for authenticated users at depth 0', () => {
        vi.mocked(usePage).mockReturnValue({
            props: {
                auth: { user: { id: 3, name: 'Other User' } },
            },
            url: '/posts/test-post',
        } as ReturnType<typeof usePage>);

        const wrapper = mount(CommentItem, {
            props: {
                comment: mockComment,
                postSlug: 'test-post',
                depth: 0,
            },
            global: {
                stubs: {
                    Avatar: {
                        template: '<div class="avatar-stub"><slot /></div>',
                    },
                    AvatarFallback: { template: '<span><slot /></span>' },
                    Button: { template: '<button><slot /></button>' },
                    Textarea: true,
                    ConfirmDialog: true,
                    CommentForm: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Repondre');
    });

    it('hides reply button at depth 1 (max depth reached)', () => {
        vi.mocked(usePage).mockReturnValue({
            props: {
                auth: { user: { id: 3, name: 'Other User' } },
            },
            url: '/posts/test-post',
        } as ReturnType<typeof usePage>);

        const wrapper = mount(CommentItem, {
            props: {
                comment: mockComment,
                postSlug: 'test-post',
                depth: 1,
            },
            global: {
                stubs: {
                    Avatar: {
                        template: '<div class="avatar-stub"><slot /></div>',
                    },
                    AvatarFallback: { template: '<span><slot /></span>' },
                    Button: { template: '<button><slot /></button>' },
                    Textarea: true,
                    ConfirmDialog: true,
                    CommentForm: true,
                },
            },
        });

        expect(wrapper.text()).not.toContain('Repondre');
    });

    it('shows "(modifie)" indicator when comment was edited', () => {
        const editedComment = createMockComment({
            ...mockComment,
            created_at: '2024-01-15T10:00:00Z',
            updated_at: '2024-01-15T11:00:00Z',
        });

        const wrapper = mount(CommentItem, {
            props: {
                comment: editedComment,
                postSlug: 'test-post',
            },
            global: {
                stubs: {
                    Avatar: {
                        template: '<div class="avatar-stub"><slot /></div>',
                    },
                    AvatarFallback: { template: '<span><slot /></span>' },
                    Button: { template: '<button><slot /></button>' },
                    Textarea: true,
                    ConfirmDialog: true,
                    CommentForm: true,
                },
            },
        });

        expect(wrapper.text()).toContain('(modifie)');
    });

    it('renders replies when present', () => {
        const commentWithReplies = createMockComment({
            ...mockComment,
            replies: [
                createMockComment({
                    id: 2,
                    parent_id: 1,
                    content: 'Réponse 1',
                    author: { id: 3, name: 'Autre' },
                }),
                createMockComment({
                    id: 3,
                    parent_id: 1,
                    content: 'Réponse 2',
                    author: { id: 4, name: 'Autre 2' },
                }),
            ],
        });

        const wrapper = mount(CommentItem, {
            props: {
                comment: commentWithReplies,
                postSlug: 'test-post',
            },
            global: {
                stubs: {
                    Avatar: {
                        template: '<div class="avatar-stub"><slot /></div>',
                    },
                    AvatarFallback: { template: '<span><slot /></span>' },
                    Button: { template: '<button><slot /></button>' },
                    Textarea: true,
                    ConfirmDialog: true,
                    CommentForm: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Réponse 1');
        expect(wrapper.text()).toContain('Réponse 2');
    });
});
