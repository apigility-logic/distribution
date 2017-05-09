;(function(global, factory) {
    var Pulse = factory();
    Pulse.create = function(config) {
        return new Pulse(config);
    };
    global.Pulse = Pulse;

})



(window, function() {
    var Pulse = function(config) {
        this.config = this.extend({
            param: [],
            speed: 500,
            delay: false,
            start: false,
            stop: false,
            hreat: null,
            pulse: null,
            disappear: null
        }, config || {});

        this.currentIndex = 0;
        this.config.start === true && this.start();
    };

    Pulse.prototype = {
        constructor: Pulse,

        interval: function(fn, speed, right) {
            var _this = this;

            this.timer = setTimeout(function() {
                if ( fn() !== false ) {
                    _this.interval(fn, speed);
                }
            }, speed);

            if ( right === true ) {
                fn();
            }
        },

        extend: function(target, source) {
            for (var key in source) {
                target[key] = source[key];
            }

            return target;
        },

        stop: function() {
            clearTimeout(this.timer);
        },

        start: function() {
            var _this = this;

            this.createTime = new Date().getTime();
            this.lastTime = this.createTime;

            this.interval(function() {
                _this._runtime();
            }, this.config.speed, !this.config.delay);
        },

        _runtime: function(node) {
            var nowTime = new Date().getTime(),
                node = {
                    createTime: this.createTime,
                    lastTime: this.lastTime,
                    nowTime: nowTime
                };

            this._hreat(node);
            this._pulse(node);
            this._disappear(node);
        },

        _hreat: function(node) {
            if ( typeof this.config.hreat === 'function' ) {
                this.config.hreat.call(this, node);
            }
        },

        _pulse: function(node) {
            var _this = this,
                differ = node.nowTime - this.createTime;

            for (var i = 0, length = this.config.param.length; i < length; i++) {
                var item = this.config.param[i];

                if ( differ <= item[0] ) {
                    if ( node.nowTime - this.lastTime >= item[1] ) {
                        if ( typeof this.config.pulse === 'function' ) {
                            this.config.pulse.call(this, node);
                        }

                        this.lastTime = node.nowTime;
                    }

                    break;
                }

                this.currentIndex = i + 1;
            }
        },

        _disappear: function(node) {
            if ( this.currentIndex === this.config.param.length ) {
                if (typeof this.config.disappear === 'function') {
                    this.config.disappear.call(this, node);
                }

                this.config.stop === true && this.stop();
            }
        }
    };

    return Pulse;
});
