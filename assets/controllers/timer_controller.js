import { Controller } from '@hotwired/stimulus';
import { useDebounce } from 'stimulus-use';
import axios from 'axios';
import { DateTime, Duration } from 'luxon';

/* stimulusFetch: 'lazy */
export default class extends Controller {
    #intervalId;
    #startIcon;
    #stopIcon;

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
        this.#startIcon = this.timerStartButtonTarget.innerHTML;
        this.#stopIcon = this.timerStopButtonTarget.innerHTML;

        if (this.timerRunningValue) {
            this.initTimer();
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

    // Called when page is first loaded to start any timers that are already running.
    initTimer() {
        let startedAt = DateTime.fromSeconds(this.timerStartTimeValue);

        // Negate the diff, otherwise we'll get a negative int.
        let diff = startedAt.diffNow(['seconds']).negate();

        this.runTimer(diff);
    }

    // Called when starting a timer AFTER the initial page load
    runTimer(diff) {
        // let totalTime = Duration.fromObject({seconds: this.timerAccumulatedTimeValue});
        let totalTime = diff.plus({seconds: this.timerAccumulatedTimeValue});

        this.#intervalId = setInterval(() => {
            totalTime = totalTime.plus({seconds: 1});

            this.timerDurationCounterTarget.innerHTML = totalTime.toFormat('h:mm:ss');
        }, 1000);
    }

    startTimer() {
        this.timerStartButtonTarget.innerHTML = '~';

        this.restartTimer();

        let diff = Duration.fromObject({seconds: 0})

        this.runTimer(diff);
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
                return response.data;
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

        this.timerAccumulatedTimeValue = response.accumulatedSeconds;
        this.timerStartTimeValue = response.restartedAt;

        this.timerStartButtonTarget.classList.toggle('hidden', true);
        this.timerStopButtonTarget.classList.toggle('hidden', false);
        this.timerStartButtonTarget.innerHTML = this.#startIcon;
    }

    async persistTimer() {
        let response = await axios
            .post(`/timer/pause/${this.timerIdValue}`)
            .then((response) => {
                return response.data.accumulatedSeconds;
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

        this.timerAccumulatedTimeValue = response;

        this.timerStartButtonTarget.classList.toggle('hidden', false);
        this.timerStopButtonTarget.classList.toggle('hidden', true);
        this.timerStopButtonTarget.innerHTML = this.#stopIcon;
    }
}