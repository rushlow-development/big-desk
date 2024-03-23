import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';
import { useDebounce } from 'stimulus-use';
import axios from 'axios';
import { DateTime, Duration } from 'luxon';

/* stimulusFetch: 'lazy */
export default class extends Controller {
    #intervalId;

    static debounces = ['runTimer', 'startTimer', 'stopTimer']

    static values = {
        timerId: String,
        timerRunning: Boolean,
        timerTotalSeconds: Number,
    }

    static targets = [
        'timerDurationCounter',
    ]

    async initialize() {
        this.component = await getComponent(this.element);
    }

    connect() {
        useDebounce(this);

        if (this.timerRunningValue) {
            this.initTimer();
        }

        window.addEventListener(`timer:start:${this.timerIdValue}`, (event) => {
            let totalSecondsDuration = Duration.fromObject({seconds: event.detail.totalSeconds});

            this.#intervalId = this.attachInterval(totalSecondsDuration);
        });

        window.addEventListener(`timer:stop:${this.timerIdValue}`, () => {
            clearInterval(this.#intervalId);

            this.timerRunningValue = false;
        });
    }

    // Called when page is first loaded to start any timers that are already running.
    initTimer() {
        let totalSecondsDuration = Duration.fromObject({seconds: this.timerTotalSecondsValue});

        this.#intervalId = this.attachInterval(totalSecondsDuration);
    }

    attachInterval(totalSecondsDuration) {
        return setInterval(() => {
            totalSecondsDuration = totalSecondsDuration.plus({seconds: 1});

            this.timerDurationCounterTarget.innerHTML = totalSecondsDuration.toFormat('h:mm:ss');
        }, 1000);
    }
}
