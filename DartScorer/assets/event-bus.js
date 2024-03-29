import { reactive } from 'vue';

// Create a reactive object to act as an event bus
const eventBus = reactive({});

// Define methods for emitting and listening to events
export const EventBus = {
    emit(event, ...args) {
        if (!eventBus[event]) return;
        eventBus[event].forEach(callback => {
            callback(...args);
        });
    },
    on(event, callback) {
        if (!eventBus[event]) eventBus[event] = [];
        eventBus[event].push(callback);
    },
};
