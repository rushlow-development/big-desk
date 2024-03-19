import { Controller } from '@hotwired/stimulus';
import axios from 'axios';

/* stimulusFetch: 'lazy */
export default class extends Controller {
    static values = {
        // todoListId: String,
    }

    static targets = [
        'timerList',
    ]

    async startTimer() {
        let response = await axios
            .post('/timer/create')
            .then((response) => {
                if (response.status !== 200) {
                    // @TODO Show an alert of something...

                    return false;
                }

                return response.data;
            })
        ;

        if (response === false) {
            // @TODO Show an alert of something...

            console.log('Ooops, something went wrong....');

            return;
        }

        // console.log(response.html.content);

        const div = document.createElement('div');
        div.classList.add('row');
        div.innerHTML = response.html.content;

        this.timerListTarget.prepend(div);
    }
}