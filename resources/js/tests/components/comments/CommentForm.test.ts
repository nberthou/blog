import CommentForm from '@/components/comments/CommentForm.vue';
import { mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';

const mockPost = vi.fn();
const mockReset = vi.fn();

vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual('@inertiajs/vue3');
    return {
        ...actual,
        useForm: vi.fn(() => ({
            processing: false,
            errors: {},
            content: '',
            parent_id: null,
            post: mockPost,
            reset: mockReset,
        })),
    };
});

describe('CommentForm', () => {
    it('renders textarea with default placeholder', () => {
        const wrapper = mount(CommentForm, {
            props: {
                postSlug: 'test-post',
            },
            global: {
                stubs: {
                    Button: {
                        template:
                            '<button :disabled="disabled"><slot /></button>',
                        props: ['disabled'],
                    },
                    Textarea: {
                        template:
                            '<textarea :placeholder="placeholder"></textarea>',
                        props: [
                            'placeholder',
                            'modelValue',
                            'autofocus',
                            'rows',
                        ],
                    },
                },
            },
        });

        const textarea = wrapper.find('textarea');
        expect(textarea.attributes('placeholder')).toBe('Votre commentaire...');
    });

    it('renders textarea with custom placeholder', () => {
        const wrapper = mount(CommentForm, {
            props: {
                postSlug: 'test-post',
                placeholder: 'Votre réponse...',
            },
            global: {
                stubs: {
                    Button: {
                        template:
                            '<button :disabled="disabled"><slot /></button>',
                        props: ['disabled'],
                    },
                    Textarea: {
                        template:
                            '<textarea :placeholder="placeholder"></textarea>',
                        props: [
                            'placeholder',
                            'modelValue',
                            'autofocus',
                            'rows',
                        ],
                    },
                },
            },
        });

        const textarea = wrapper.find('textarea');
        expect(textarea.attributes('placeholder')).toBe('Votre réponse...');
    });

    it('renders submit button with default label', () => {
        const wrapper = mount(CommentForm, {
            props: {
                postSlug: 'test-post',
            },
            global: {
                stubs: {
                    Button: {
                        template:
                            '<button :disabled="disabled"><slot /></button>',
                        props: ['disabled'],
                    },
                    Textarea: {
                        template:
                            '<textarea :placeholder="placeholder"></textarea>',
                        props: [
                            'placeholder',
                            'modelValue',
                            'autofocus',
                            'rows',
                        ],
                    },
                },
            },
        });

        expect(wrapper.text()).toContain('Commenter');
    });

    it('renders submit button with custom label', () => {
        const wrapper = mount(CommentForm, {
            props: {
                postSlug: 'test-post',
                buttonLabel: 'Répondre',
            },
            global: {
                stubs: {
                    Button: {
                        template:
                            '<button :disabled="disabled"><slot /></button>',
                        props: ['disabled'],
                    },
                    Textarea: {
                        template:
                            '<textarea :placeholder="placeholder"></textarea>',
                        props: [
                            'placeholder',
                            'modelValue',
                            'autofocus',
                            'rows',
                        ],
                    },
                },
            },
        });

        expect(wrapper.text()).toContain('Répondre');
    });

    it('submit button is disabled when content is empty', () => {
        const wrapper = mount(CommentForm, {
            props: {
                postSlug: 'test-post',
            },
            global: {
                stubs: {
                    Button: {
                        template:
                            '<button :disabled="disabled"><slot /></button>',
                        props: ['disabled'],
                    },
                    Textarea: {
                        template:
                            '<textarea :placeholder="placeholder"></textarea>',
                        props: [
                            'placeholder',
                            'modelValue',
                            'autofocus',
                            'rows',
                        ],
                    },
                },
            },
        });

        const button = wrapper.find('button');
        expect(button.attributes('disabled')).toBeDefined();
    });

    it('generates correct store URL from postSlug', () => {
        const wrapper = mount(CommentForm, {
            props: {
                postSlug: 'my-awesome-post',
            },
            global: {
                stubs: {
                    Button: {
                        template:
                            '<button :disabled="disabled"><slot /></button>',
                        props: ['disabled'],
                    },
                    Textarea: {
                        template:
                            '<textarea :placeholder="placeholder"></textarea>',
                        props: [
                            'placeholder',
                            'modelValue',
                            'autofocus',
                            'rows',
                        ],
                    },
                },
            },
        });

        // The component should generate URL: /posts/my-awesome-post/comments
        // We can verify this by checking the component's computed property
        expect(wrapper.vm.storeUrl).toBe('/posts/my-awesome-post/comments');
    });
});
