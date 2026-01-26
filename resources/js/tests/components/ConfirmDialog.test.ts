import ConfirmDialog from '@/components/ConfirmDialog.vue';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

// Note: Testing Dialog components with Teleport is complex.
// These tests verify the component structure and props handling.
// For full integration tests, consider using Playwright or Cypress.

describe('ConfirmDialog', () => {
    const defaultProps = {
        open: true,
        title: 'Confirmer la suppression',
        description: 'Êtes-vous sûr ?',
        confirmLabel: 'Supprimer',
        cancelLabel: 'Annuler',
    };

    it('mounts without errors', () => {
        const wrapper = mount(ConfirmDialog, {
            props: { open: false },
            global: {
                stubs: {
                    teleport: true,
                    Dialog: {
                        template: '<div><slot /></div>',
                        props: ['open'],
                    },
                    DialogContent: {
                        template: '<div><slot /></div>',
                    },
                    DialogHeader: {
                        template: '<div><slot /></div>',
                    },
                    DialogTitle: {
                        template: '<span><slot /></span>',
                    },
                    DialogDescription: {
                        template: '<p><slot /></p>',
                    },
                    DialogFooter: {
                        template: '<div><slot /></div>',
                    },
                    DialogClose: {
                        template: '<span><slot /></span>',
                    },
                    Button: {
                        template:
                            '<button :disabled="disabled" @click="$emit(\'click\')"><slot /></button>',
                        props: ['variant', 'disabled'],
                    },
                },
            },
        });

        expect(wrapper.exists()).toBe(true);
    });

    it('renders title when open', () => {
        const wrapper = mount(ConfirmDialog, {
            props: defaultProps,
            global: {
                stubs: {
                    teleport: true,
                    Dialog: {
                        template: '<div v-if="open"><slot /></div>',
                        props: ['open'],
                    },
                    DialogContent: {
                        template: '<div><slot /></div>',
                    },
                    DialogHeader: {
                        template: '<div><slot /></div>',
                    },
                    DialogTitle: {
                        template: '<span><slot /></span>',
                    },
                    DialogDescription: {
                        template: '<p><slot /></p>',
                    },
                    DialogFooter: {
                        template: '<div><slot /></div>',
                    },
                    DialogClose: {
                        template: '<span><slot /></span>',
                    },
                    Button: {
                        template:
                            '<button :disabled="disabled" @click="$emit(\'click\')"><slot /></button>',
                        props: ['variant', 'disabled'],
                    },
                },
            },
        });

        expect(wrapper.text()).toContain('Confirmer la suppression');
    });

    it('renders description when open', () => {
        const wrapper = mount(ConfirmDialog, {
            props: defaultProps,
            global: {
                stubs: {
                    teleport: true,
                    Dialog: {
                        template: '<div v-if="open"><slot /></div>',
                        props: ['open'],
                    },
                    DialogContent: {
                        template: '<div><slot /></div>',
                    },
                    DialogHeader: {
                        template: '<div><slot /></div>',
                    },
                    DialogTitle: {
                        template: '<span><slot /></span>',
                    },
                    DialogDescription: {
                        template: '<p><slot /></p>',
                    },
                    DialogFooter: {
                        template: '<div><slot /></div>',
                    },
                    DialogClose: {
                        template: '<span><slot /></span>',
                    },
                    Button: {
                        template:
                            '<button :disabled="disabled" @click="$emit(\'click\')"><slot /></button>',
                        props: ['variant', 'disabled'],
                    },
                },
            },
        });

        expect(wrapper.text()).toContain('Êtes-vous sûr ?');
    });

    it('renders button labels', () => {
        const wrapper = mount(ConfirmDialog, {
            props: defaultProps,
            global: {
                stubs: {
                    teleport: true,
                    Dialog: {
                        template: '<div v-if="open"><slot /></div>',
                        props: ['open'],
                    },
                    DialogContent: {
                        template: '<div><slot /></div>',
                    },
                    DialogHeader: {
                        template: '<div><slot /></div>',
                    },
                    DialogTitle: {
                        template: '<span><slot /></span>',
                    },
                    DialogDescription: {
                        template: '<p><slot /></p>',
                    },
                    DialogFooter: {
                        template: '<div><slot /></div>',
                    },
                    DialogClose: {
                        template: '<span><slot /></span>',
                    },
                    Button: {
                        template:
                            '<button :disabled="disabled" @click="$emit(\'click\')"><slot /></button>',
                        props: ['variant', 'disabled'],
                    },
                },
            },
        });

        expect(wrapper.text()).toContain('Supprimer');
        expect(wrapper.text()).toContain('Annuler');
    });

    it('shows loading text when loading', () => {
        const wrapper = mount(ConfirmDialog, {
            props: {
                ...defaultProps,
                loading: true,
            },
            global: {
                stubs: {
                    teleport: true,
                    Dialog: {
                        template: '<div v-if="open"><slot /></div>',
                        props: ['open'],
                    },
                    DialogContent: {
                        template: '<div><slot /></div>',
                    },
                    DialogHeader: {
                        template: '<div><slot /></div>',
                    },
                    DialogTitle: {
                        template: '<span><slot /></span>',
                    },
                    DialogDescription: {
                        template: '<p><slot /></p>',
                    },
                    DialogFooter: {
                        template: '<div><slot /></div>',
                    },
                    DialogClose: {
                        template: '<span><slot /></span>',
                    },
                    Button: {
                        template:
                            '<button :disabled="disabled" @click="$emit(\'click\')"><slot /></button>',
                        props: ['variant', 'disabled'],
                    },
                },
            },
        });

        expect(wrapper.text()).toContain('Chargement...');
    });

    it('emits confirm event when confirm button clicked', async () => {
        const wrapper = mount(ConfirmDialog, {
            props: defaultProps,
            global: {
                stubs: {
                    teleport: true,
                    Dialog: {
                        template: '<div v-if="open"><slot /></div>',
                        props: ['open'],
                    },
                    DialogContent: {
                        template: '<div><slot /></div>',
                    },
                    DialogHeader: {
                        template: '<div><slot /></div>',
                    },
                    DialogTitle: {
                        template: '<span><slot /></span>',
                    },
                    DialogDescription: {
                        template: '<p><slot /></p>',
                    },
                    DialogFooter: {
                        template: '<div><slot /></div>',
                    },
                    DialogClose: {
                        template: '<span><slot /></span>',
                    },
                    Button: {
                        template:
                            '<button :disabled="disabled" @click="$emit(\'click\')"><slot /></button>',
                        props: ['variant', 'disabled'],
                        emits: ['click'],
                    },
                },
            },
        });

        const buttons = wrapper.findAll('button');
        const confirmBtn = buttons.find((b) => b.text().includes('Supprimer'));
        await confirmBtn?.trigger('click');

        expect(wrapper.emitted('confirm')).toBeTruthy();
    });

    it('emits cancel event when cancel button clicked', async () => {
        const wrapper = mount(ConfirmDialog, {
            props: defaultProps,
            global: {
                stubs: {
                    teleport: true,
                    Dialog: {
                        template: '<div v-if="open"><slot /></div>',
                        props: ['open'],
                    },
                    DialogContent: {
                        template: '<div><slot /></div>',
                    },
                    DialogHeader: {
                        template: '<div><slot /></div>',
                    },
                    DialogTitle: {
                        template: '<span><slot /></span>',
                    },
                    DialogDescription: {
                        template: '<p><slot /></p>',
                    },
                    DialogFooter: {
                        template: '<div><slot /></div>',
                    },
                    DialogClose: {
                        template:
                            '<span @click="$emit(\'click\')"><slot /></span>',
                        emits: ['click'],
                    },
                    Button: {
                        template:
                            '<button :disabled="disabled" @click="$emit(\'click\')"><slot /></button>',
                        props: ['variant', 'disabled'],
                        emits: ['click'],
                    },
                },
            },
        });

        const buttons = wrapper.findAll('button');
        const cancelBtn = buttons.find((b) => b.text().includes('Annuler'));
        await cancelBtn?.trigger('click');

        expect(wrapper.emitted('cancel')).toBeTruthy();
    });

    it('uses default props when not provided', () => {
        const wrapper = mount(ConfirmDialog, {
            props: { open: true },
            global: {
                stubs: {
                    teleport: true,
                    Dialog: {
                        template: '<div v-if="open"><slot /></div>',
                        props: ['open'],
                    },
                    DialogContent: {
                        template: '<div><slot /></div>',
                    },
                    DialogHeader: {
                        template: '<div><slot /></div>',
                    },
                    DialogTitle: {
                        template: '<span><slot /></span>',
                    },
                    DialogDescription: {
                        template: '<p><slot /></p>',
                    },
                    DialogFooter: {
                        template: '<div><slot /></div>',
                    },
                    DialogClose: {
                        template: '<span><slot /></span>',
                    },
                    Button: {
                        template:
                            '<button :disabled="disabled" @click="$emit(\'click\')"><slot /></button>',
                        props: ['variant', 'disabled'],
                    },
                },
            },
        });

        expect(wrapper.text()).toContain('Confirmer');
        expect(wrapper.text()).toContain('Annuler');
    });
});
