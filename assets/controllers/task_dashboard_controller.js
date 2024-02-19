import { Controller } from '@hotwired/stimulus';
import axios from 'axios';

/* stimulusFetch: 'lazy */
export default class extends Controller {
    static values = {
        todoListId: String,
    }

    async removeTask($event) {
        let response = await axios
            .post(`/todo/${this.todoListIdValue}/task/remove/${$event.params['taskIndexId']}`)
            .then((response) => {
                if (response.status !== 200) {
                    console.log(response);

                    return false;
                }

                return true;
            });

        if (response === true) {
            window.location.reload();
        }
    }
}