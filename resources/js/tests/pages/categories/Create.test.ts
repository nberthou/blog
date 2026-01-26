import Create from '@/pages/categories/Create.vue';
import { useForm } from '@inertiajs/vue3';
import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';

const mockPost = vi.fn();

vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual('@inertiajs/vue3');
    return {
        ...actual,
        usePage: vi.fn(() => ({
            props: {
                auth: { user: { id: 1, name: 'Admin' } },
            },
            url: '/categories-create',
        })),
        useForm: vi.fn(() => ({
            name: '',
            description: '',
            processing: false,
            errors: {},
            post: mockPost,
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

describe('Categories Create Page', () => {
    beforeEach(() => {
        mockPost.mockClear();
    });

    it('renders the page title', () => {
        const wrapper = mount(Create, {
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                },
            },
        });

        expect(wrapper.text()).toContain('Nouvelle catégorie');
    });

    it('renders the form fields', () => {
        const wrapper = mount(Create, {
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                },
            },
        });

        expect(wrapper.text()).toContain('Nom');
        expect(wrapper.text()).toContain('Description');
    });

    it('renders back link to categories index', () => {
        const wrapper = mount(Create, {
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                },
            },
        });

        expect(wrapper.text()).toContain('Retour aux catégories');
    });

    it('renders submit and cancel buttons', () => {
        const wrapper = mount(Create, {
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                },
            },
        });

        expect(wrapper.text()).toContain('Créer la catégorie');
        expect(wrapper.text()).toContain('Annuler');
    });

    it('shows processing state on submit button', () => {
        vi.mocked(useForm).mockReturnValue({
            name: '',
            description: '',
            processing: true,
            errors: {},
            post: mockPost,
        } as ReturnType<typeof useForm>);

        const wrapper = mount(Create, {
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                },
            },
        });

        expect(wrapper.text()).toContain('Création...');
    });

    it('shows validation error for name field', () => {
        vi.mocked(useForm).mockReturnValue({
            name: '',
            description: '',
            processing: false,
            errors: { name: 'Le nom est obligatoire.' },
            post: mockPost,
        } as ReturnType<typeof useForm>);

        const wrapper = mount(Create, {
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                },
            },
        });

        expect(wrapper.text()).toContain('Le nom est obligatoire.');
    });

    it('renders name input with placeholder', () => {
        const wrapper = mount(Create, {
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                },
            },
        });

        const nameInput = wrapper.find('input#name');
        expect(nameInput.attributes('placeholder')).toContain('Technologie');
    });

    it('renders description textarea with placeholder', () => {
        const wrapper = mount(Create, {
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                },
            },
        });

        const descTextarea = wrapper.find('textarea#description');
        expect(descTextarea.attributes('placeholder')).toContain(
            'courte description',
        );
    });

    it('indicates name field is required', () => {
        const wrapper = mount(Create, {
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                },
            },
        });

        expect(wrapper.text()).toContain('Nom *');
    });

    it('indicates description field is optional', () => {
        const wrapper = mount(Create, {
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                },
            },
        });

        // Description label should not have asterisk
        expect(wrapper.text()).toContain('Description');
        expect(wrapper.text()).not.toContain('Description *');
    });
});
