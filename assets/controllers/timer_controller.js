import { Controller } from '@hotwired/stimulus';
import { useDebounce } from 'stimulus-use';
import axios from 'axios';
import { DateTime } from 'luxon';

/* stimulusFetch: 'lazy */
export default class extends Controller {
    #intervalId;

    static debounces = ['runTimer', 'startTimer', 'stopTimer']

    static values = {
        timerId: String,
        timerRunning: Boolean,
        timerStartTime: Number,
        timerAccumulatedTime: Number,
    }

    static targets = [
        'timerDurationCounter',
        'timerStartButton',
        'timerStopButton',
    ]

    connect() {
        useDebounce(this);

        this.setButtonVisibility();

        if (this.timerRunningValue) {
            this.runTimer();
        }
    }

    setButtonVisibility() {
        if (this.timerRunningValue) {
            this.timerStartButtonTarget.classList.toggle('hidden', true);
            this.timerStopButtonTarget.classList.toggle('hidden', false);

            return;
        }

        this.timerStartButtonTarget.classList.toggle('hidden', false);
        this.timerStopButtonTarget.classList.toggle('hidden', true);
    }

    runTimer() {
        const startedAt = DateTime.fromSeconds(this.timerStartTimeValue);

        this.#intervalId = setInterval(() => {
            const duration = DateTime.now().diff(startedAt, ['hours', 'minutes', 'seconds']);

            this.timerDurationCounterTarget.innerHTML = duration.toFormat('h:mm:ss');
        }, 1000);
    }

    startTimer() {
        this.timerStartButtonTarget.innerHTML = '~';

        const startedAt = DateTime.fromSeconds(this.timerStartTimeValue);

        this.#intervalId = setInterval(() => {
            const duration = DateTime.now().diff(startedAt, ['hours', 'minutes', 'seconds']);

            this.timerDurationCounterTarget.innerHTML = duration.toFormat('h:mm:ss');
        }, 1000);

        this.restartTimer();
    }

    stopTimer() {
        this.timerStopButtonTarget.innerHTML = '~';

        clearInterval(this.#intervalId);

        this.timerRunningValue = false;

        this.persistTimer();
    }

    async restartTimer() {
        let response = await axios
            .post(`/timer/start/${this.timerIdValue}`)
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
            this.timerStartButtonTarget.innerHTML = 'Oops';

            clearInterval(this.#intervalId);

            return;
        }

        this.timerStartButtonTarget.classList.toggle('hidden', true);
        this.timerStopButtonTarget.classList.toggle('hidden', false);
        this.timerStartButtonTarget.innerHTML = 'Start';
    }

    async persistTimer() {
        let response = await axios
            .post(`/timer/pause/${this.timerIdValue}`)
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

        this.timerStartButtonTarget.classList.toggle('hidden', false);
        this.timerStopButtonTarget.classList.toggle('hidden', true);
        this.timerStopButtonTarget.innerHTML = 'Stop';
    }
}