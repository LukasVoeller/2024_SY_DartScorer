import { reactive } from 'vue';

// Define the type for the callback function
type EventCallback = (...args: any[]) => void;

// Create a reactive object to act as an event bus
const eventBus: Record<string, EventCallback[]> = reactive({});

// Define methods for emitting and listening to events
export const EventBus = {
    emit(event: string, ...args: any[]) {
        if (!eventBus[event]) return;
        eventBus[event].forEach(callback => {
            callback(...args);
        });
    },
    on(event: string, callback: EventCallback) {
        if (!eventBus[event]) eventBus[event] = [];
        eventBus[event].push(callback);
    },
};
