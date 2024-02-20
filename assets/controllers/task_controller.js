import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy */
export default class extends Controller {
    static targets = [
        'taskList'
    ];

    static values = {
        index: Number,
        formPrototype: String,
    }

    connect() {
        let btn = document.querySelector('#add-task');

        for (let i   = 0; i < this.taskListTarget.children.length; i++) {
            this.addRemoveButton(this.taskListTarget.children[i]);
        }
    }

    addTaskForm() {
        let lineItem = document.createElement('div');
        lineItem.classList.add('input-group', 'mb-3')

        lineItem.innerHTML = this.formPrototypeValue.replace(/__name__/g, this.indexValue);

        this.addRemoveButton(lineItem);

        this.taskListTarget.appendChild(lineItem);

        this.indexValue++;
    }

    addRemoveButton(task) {
        let removeButton = document.createElement('button');
        removeButton.innerText = 'X';
        removeButton.classList.add('btn', 'btn-outline-danger', 'ms-2');

        removeButton.addEventListener('click', (e) => {
            e.preventDefault();
            task.remove();
        })

        task.appendChild(removeButton);
    }
}
