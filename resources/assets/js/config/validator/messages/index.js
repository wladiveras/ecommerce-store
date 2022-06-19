import AsyncValidator from 'async-validator'
import messages from "./list"

AsyncValidator.prototype.messages = function () {
    return messages;
}