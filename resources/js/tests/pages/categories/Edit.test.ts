import Edit from '@/pages/categories/Edit.vue';
import { useForm } from '@inertiajs/vue3';
import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { createMockCategory } from '../../helpers';

const mockPut = vi.fn();

vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual('@inertiajs/vue3');
    return {
        ...actual,
        usePage: vi.fn(() => ({
            props: {
                auth: { user: { id: 1, name: 'Admin' } },
            },
            url: '/categories/technologie/edit',
        })),
        useForm: vi.fn((initialData) => ({
            ...initialData,
            processing: false,
            errors: {},
            put: mockPut,
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

describe('Categories Edit Page', () => {
    const mockCategory = createMockCategory({
        id: 1,
        name: 'Technologie',
        slug: 'technologie',
        description: 'Articles sur la technologie',
    });

    beforeEach(() => {
        mockPut.mockClear();
    });

    it('renders the page title', () => {
        const wrapper = mount(Edit, {
            props: {
                category: mockCategory,
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                },
            },
        });

        expect(wrapper.text()).toContain('Modifier la catégorie');
    });

    it('renders the form fields with category data', () => {
        const wrapper = mount(Edit, {
            props: {
                category: mockCategory,
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                },
            },
        });

        expect(wrapper.text()).toContain('Nom');
        expect(wrapper.text()).toContain('Slug');
        expect(wrapper.text()).toContain('Description');
    });

    it('renders back link to categories index', () => {
        const wrapper = mount(Edit, {
            props: {
                category: mockCategory,
            },
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
        const wrapper = mount(Edit, {
            props: {
                category: mockCategory,
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                },
            },
        });

        expect(wrapper.text()).toContain('Enregistrer');
        expect(wrapper.text()).toContain('Annuler');
    });

    it('shows processing state on submit button', () => {
        vi.mocked(useForm).mockReturnValue({
            name: 'Technologie',
            slug: 'technologie',
            description: 'Articles sur la technologie',
            processing: true,
            errors: {},
            put: mockPut,
        } as ReturnType<typeof useForm>);

        const wrapper = mount(Edit, {
            props: {
                category: mockCategory,
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                },
            },
        });

        expect(wrapper.text()).toContain('Enregistrement...');
    });

    it('shows validation error for slug field', () => {
        vi.mocked(useForm).mockReturnValue({
            name: 'Technologie',
            slug: 'existing-slug',
            description: '',
            processing: false,
            errors: {
                slug: 'Ce slug est déjà utilisé par une autre catégorie.',
            },
            put: mockPut,
        } as ReturnType<typeof useForm>);

        const wrapper = mount(Edit, {
            props: {
                category: mockCategory,
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                },
            },
        });

        expect(wrapper.text()).toContain('Ce slug est déjà utilisé');
    });

    it('shows slug field helper text', () => {
        const wrapper = mount(Edit, {
            props: {
                category: mockCategory,
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                },
            },
        });

        expect(wrapper.text()).toContain(
            "Identifiant unique utilisé dans l'URL",
        );
    });

    it('initializes form with category data', () => {
        const useFormMock = vi.mocked(useForm);

        mount(Edit, {
            props: {
                category: mockCategory,
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                },
            },
        });

        expect(useFormMock).toHaveBeenCalledWith({
            name: 'Technologie',
            slug: 'technologie',
            description: 'Articles sur la technologie',
        });
    });

    it('handles category without description', () => {
        const categoryWithoutDesc = createMockCategory({
            id: 1,
            name: 'Test',
            slug: 'test',
            description: null,
        });

        vi.mocked(useForm).mockReturnValue({
            name: 'Test',
            slug: 'test',
            description: '',
            processing: false,
            errors: {},
            put: mockPut,
        } as ReturnType<typeof useForm>);

        const wrapper = mount(Edit, {
            props: {
                category: categoryWithoutDesc,
            },
            global: {
                stubs: {
                    AppLayout: {
                        template: '<div><slot /></div>',
                    },
                },
            },
        });

        expect(wrapper.exists()).toBe(true);
    });
});
