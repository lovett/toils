/* eslint-disable no-unused-vars */
function prefill (e) {
    'use strict';

    var i, changeEvent, id, trigger, target;

    trigger = e.trigger || e.srcElement;

    if (trigger.hasAttributes() === false) {
        return;
    }

    for (i = 0; i < trigger.attributes.length; i++) {
        if (trigger.attributes[i].name.indexOf('data-') !== 0) {
            continue;
        }

        id = trigger.attributes[i].name.replace('data-', '');

        target = document.getElementById(id);

        if (!target) {
            continue;
        }

        if (target.nodeName === 'TEXTAREA') {
            target.innerHTML = trigger.attributes[i].value;
            continue;
        }

        target.value = trigger.attributes[i].value;

        if (target.nodeName === 'SELECT') {
            changeEvent = new window.Event('change');
            target.dispatchEvent(changeEvent);
        }
    }
}
