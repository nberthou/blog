export function useRelativeTime() {
    const formatRelativeTime = (dateString: string): string => {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor(
            (now.getTime() - date.getTime()) / 1000,
        );

        if (diffInSeconds < 60) {
            return "À l'instant";
        }

        const diffInMinutes = Math.floor(diffInSeconds / 60);
        if (diffInMinutes < 60) {
            return `Il y a ${diffInMinutes} min`;
        }

        const diffInHours = Math.floor(diffInMinutes / 60);
        if (diffInHours < 24) {
            return `Il y a ${diffInHours}h`;
        }

        const diffInDays = Math.floor(diffInHours / 24);
        if (diffInDays < 7) {
            return `Il y a ${diffInDays} jour${diffInDays > 1 ? 's' : ''}`;
        }

        const diffInWeeks = Math.floor(diffInDays / 7);
        if (diffInWeeks < 4) {
            return `Il y a ${diffInWeeks} semaine${diffInWeeks > 1 ? 's' : ''}`;
        }

        const diffInMonths = Math.floor(diffInDays / 30);
        if (diffInMonths < 12) {
            return `Il y a ${diffInMonths} mois`;
        }

        const diffInYears = Math.floor(diffInDays / 365);
        return `Il y a ${diffInYears} an${diffInYears > 1 ? 's' : ''}`;
    };

    return {
        formatRelativeTime,
    };
}
