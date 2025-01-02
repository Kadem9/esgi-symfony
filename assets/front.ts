import './style/front/front.scss'
import {AlertMessage} from "./elements/AlertMessage.ts";
import registerPreactCustomElement from "./function/registerPreactCustomElement.ts";
import {Festival} from "./elements/Front/ApiFestival/Festival.tsx";

customElements.define('alert-message', AlertMessage);
registerPreactCustomElement(Festival, 'festival-api', [], {});