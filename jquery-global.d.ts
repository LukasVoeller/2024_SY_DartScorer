import 'jquery';

declare global {
    interface JQuery {
        selectpicker(method?: string | any): JQuery;
    }
}

export {};
