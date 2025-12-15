import { mount } from '@vue/test-utils';
import { describe, it, expect, vi } from 'vitest';
import Message from '@/components/chat/Message.vue';

// Mock motion-v
vi.mock('motion-v', () => ({
    Motion: { template: '<div><slot /></div>' }
}));

// Mock unplugin-icons
vi.mock('~icons/solar/copy-linear', () => ({ default: { template: '<svg />' } }));
vi.mock('~icons/solar/check-circle-bold', () => ({ default: { template: '<svg />' } }));
vi.mock('~icons/solar/restart-linear', () => ({ default: { template: '<svg />' } }));
vi.mock('~icons/solar/download-linear', () => ({ default: { template: '<svg />' } }));

describe('Message.vue', () => {
    it('renders content correctly', () => {
        const wrapper = mount(Message, {
            props: {
                variant: 'Responder/Text',
                content: 'Hello world',
                timestamp: '12:00 PM'
            }
        });

        expect(wrapper.text()).toContain('Hello world');
        expect(wrapper.text()).toContain('12:00 PM');
    });
});
