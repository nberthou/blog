import CommentSection from '@/components/comments/CommentSection.vue';
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
            content: '',
            parent_id: null,
            post: vi.fn(),
            reset: vi.fn(),
        })),
        Link: {
            name: 'Link',
            template: '<a :href="href"><slot /></a>',
            props: ['href'],
        },
    };
});

describe('CommentSection', () => {
    const mockComments = [
        createMockComment({
            id: 1,
            content: 'Premier commentaire',
            author: { id: 1, name: 'Alice' },
        }),
        createMockComment({
            id: 2,
            content: 'Deuxième commentaire',
            author: { id: 2, name: 'Bob' },
            replies: [
                createMockComment({
                    id: 3,
                    parent_id: 2,
                    content: 'Réponse au commentaire',
                    author: { id: 1, name: 'Alice' },
                }),
            ],
        }),
    ];

    beforeEach(() => {
        vi.mocked(usePage).mockReturnValue({
            props: {
                auth: { user: null },
            },
            url: '/posts/test-post',
        } as ReturnType<typeof usePage>);
    });

    it('renders the comments header with count', () => {
        const wrapper = mount(CommentSection, {
            props: {
                comments: mockComments,
                postSlug: 'test-post',
                commentsCount: 3,
            },
            global: {
                stubs: {
                    CommentForm: true,
                    CommentItem: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Commentaires');
        expect(wrapper.text()).toContain('(3)');
    });

    it('shows login prompt for non-authenticated users', () => {
        const wrapper = mount(CommentSection, {
            props: {
                comments: mockComments,
                postSlug: 'test-post',
                commentsCount: 2,
            },
            global: {
                stubs: {
                    CommentForm: true,
                    CommentItem: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Connectez-vous');
        expect(wrapper.text()).toContain('pour laisser un commentaire');
    });

    it('shows comment form for authenticated users', () => {
        vi.mocked(usePage).mockReturnValue({
            props: {
                auth: { user: { id: 1, name: 'Test User' } },
            },
            url: '/posts/test-post',
        } as ReturnType<typeof usePage>);

        const wrapper = mount(CommentSection, {
            props: {
                comments: mockComments,
                postSlug: 'test-post',
                commentsCount: 2,
            },
            global: {
                stubs: {
                    CommentForm: {
                        template:
                            '<div class="comment-form-stub">Comment Form</div>',
                    },
                    CommentItem: true,
                },
            },
        });

        expect(wrapper.find('.comment-form-stub').exists()).toBe(true);
    });

    it('displays empty state when no comments', () => {
        const wrapper = mount(CommentSection, {
            props: {
                comments: [],
                postSlug: 'test-post',
                commentsCount: 0,
            },
            global: {
                stubs: {
                    CommentForm: true,
                    CommentItem: true,
                },
            },
        });

        expect(wrapper.text()).toContain('Aucun commentaire pour le moment');
        expect(wrapper.text()).toContain('Soyez le premier');
    });

    it('renders CommentItem for each root comment', () => {
        const wrapper = mount(CommentSection, {
            props: {
                comments: mockComments,
                postSlug: 'test-post',
                commentsCount: 3,
            },
            global: {
                stubs: {
                    CommentForm: true,
                    CommentItem: {
                        template:
                            '<div class="comment-item-stub">{{ comment.content }}</div>',
                        props: ['comment', 'postSlug'],
                    },
                },
            },
        });

        const commentItems = wrapper.findAll('.comment-item-stub');
        expect(commentItems).toHaveLength(2);
    });
});
