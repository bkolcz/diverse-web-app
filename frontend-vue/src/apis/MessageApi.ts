export const MessageApi = {
    root: import.meta.env.VITE_MESSAGE_API,
    routes: {
        base: "/api",
        get message(): string { return this.base + "/message"; },
    },
    get base(): string { return `${this.root}${this.routes.base}`; },
    get message(): string { return `${this.root}${this.routes.message}`; },
};
