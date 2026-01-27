import { useRelativeTime } from '@/composables/useRelativeTime';
import { beforeEach, describe, expect, it, vi } from 'vitest';

describe('useRelativeTime', () => {
    const { formatRelativeTime } = useRelativeTime();

    beforeEach(() => {
        vi.useFakeTimers();
        vi.setSystemTime(new Date('2024-01-15T12:00:00Z'));
    });

    it('returns "À l\'instant" for dates less than a minute ago', () => {
        const date = new Date('2024-01-15T11:59:30Z').toISOString();
        expect(formatRelativeTime(date)).toBe("À l'instant");
    });

    it('returns minutes for dates less than an hour ago', () => {
        const date = new Date('2024-01-15T11:45:00Z').toISOString();
        expect(formatRelativeTime(date)).toBe('Il y a 15 min');
    });

    it('returns hours for dates less than a day ago', () => {
        const date = new Date('2024-01-15T10:00:00Z').toISOString();
        expect(formatRelativeTime(date)).toBe('Il y a 2h');
    });

    it('returns days for dates less than a week ago', () => {
        const date = new Date('2024-01-13T12:00:00Z').toISOString();
        expect(formatRelativeTime(date)).toBe('Il y a 2 jours');
    });

    it('returns singular day for one day ago', () => {
        const date = new Date('2024-01-14T12:00:00Z').toISOString();
        expect(formatRelativeTime(date)).toBe('Il y a 1 jour');
    });

    it('returns weeks for dates less than a month ago', () => {
        const date = new Date('2024-01-01T12:00:00Z').toISOString();
        expect(formatRelativeTime(date)).toBe('Il y a 2 semaines');
    });

    it('returns singular week for one week ago', () => {
        const date = new Date('2024-01-08T12:00:00Z').toISOString();
        expect(formatRelativeTime(date)).toBe('Il y a 1 semaine');
    });

    it('returns months for dates less than a year ago', () => {
        const date = new Date('2023-11-15T12:00:00Z').toISOString();
        expect(formatRelativeTime(date)).toBe('Il y a 2 mois');
    });

    it('returns years for dates more than a year ago', () => {
        const date = new Date('2022-01-15T12:00:00Z').toISOString();
        expect(formatRelativeTime(date)).toBe('Il y a 2 ans');
    });

    it('returns singular year for one year ago', () => {
        const date = new Date('2023-01-15T12:00:00Z').toISOString();
        expect(formatRelativeTime(date)).toBe('Il y a 1 an');
    });
});
