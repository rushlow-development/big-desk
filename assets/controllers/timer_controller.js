import { Controller } from '@hotwired/stimulus';
import { useDebounce } from 'stimulus-use';
import axios from 'axios';
import { DateTime } from 'luxon';

/* stimulusFetch: 'lazy */
export default class extends Controller {
    #intervalId;

    static debounces = ['runTimer', 'stopTimer']

    static values = {
        timerId: String,
        timerRunning: Boolean,
        timerStartTime: Number,
    }

    static targets = [
        'timerDurationCounter',
        'timerStopButton',
    ]

    connect() {
        useDebounce(this);

        if (this.timerRunningValue) {
            this.runTimer();

            return;
        }

        this.timerStopButtonTarget.remove();
    }

    runTimer() {
        const startedAt = DateTime.fromSeconds(this.timerStartTimeValue);

        this.#intervalId = setInterval(() => {
            const duration = DateTime.now().diff(startedAt, ['hours', 'minutes', 'seconds']);

            this.timerDurationCounterTarget.innerHTML = duration.toFormat('h:mm:ss');
        }, 1000);
    }

    stopTimer() {
        this.timerStopButtonTarget.innerHTML = '~';
        clearInterval(this.#intervalId);
        this.timerRunningValue = false;
        this.persistTimer();
    }

    async persistTimer() {
        let response = await axios
            .post(`/timer/stop/${this.timerIdValue}`)
            .then((response) => {
                return response.data.message;
            })
            .catch(function (error) {
                console.log(error);

                return false;
            })
        ;

        if (response === false) {
            // @todo do something
            this.timerStopButtonTarget.innerHTML = 'Oops';

            this.runTimer();

            return;
        }

        this.timerStopButtonTarget.innerHTML = '';
        this.timerStopButtonTarget.remove();
    }


    // async startTimer() {
    //     let response = await axios
    //         .post('/timer/start')
    //         .then((response) => {
    //             if (response.status !== 200) {
    //                 // @TODO Show an alert of something...
    //
    //                 return false;
    //             }
    //
    //             return response.data;
    //         })
    //     ;
    //
    //     if (response === false) {
    //         // @TODO Show an alert of something...
    //
    //         console.log('Ooops, something went wrong....');
    //
    //         return;
    //     }
    //
    //     console.log(response.html.content);
    //
    //     const div = document.createElement('div');
    //     div.classList.add('row');
    //     div.innerHTML = response.html.content;
    //
    //     this.timerListTarget.prepend(div);
    // }
}