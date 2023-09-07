export const MessageApi = {
    root: import.meta.env.VITE_MESSAGE_API,
    messageKeyPattern: /(?<uuid>\w{8}(?:-\w{4}){3}-\w{12}).(?<time>\d{17})/,
    datePattern: /(?<year>\d{4})(?<month>\d{2})(?<day>\d{2})(?<hour>\d{2})(?<min>\d{2})(?<sec>\d{2})(?<milis>\d{3})/,
    routes: {
        base: "/api",
        get message(): string { return this.base + "/message"; },
    },
    get base(): string { return `${this.root}${this.routes.base}`; },
    get message(): string { return `${this.root}${this.routes.message}`; },
    get messageKeyRegExp(): RegExp { return RegExp(MessageApi.messageKeyPattern) },
    get dateRegExp(): RegExp { return RegExp(MessageApi.datePattern) },
};
