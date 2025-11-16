const Validator = function (value, validates) {
    const results = [];
    validates.map((keys) => {
        const [name, arg] = keys.split(":");
        let status = false;
        switch (name) {
            case "required":
                status = value != "" && value != null;
                break;
            case "email":
                status = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(
                    value
                );
                break;
            case "number":
                status = /^\d+$/.test(value);
                break;
            case "min":
                status = value.length >= arg;
                break;
            case "max":
                status = value.length <= arg;
                break;
            case "pattern":
                status = new RegExp(arg).test(value);
                break;
            default:
                status = false;
                break;
        }
        results.push(status);
    });
    return results.every((item) => item === false);
};

const ValidatorWithError = function (value, validates) {
    const results = [];
    validates.map((keys) => {
        const [name, arg] = keys.split(":");
        switch (name) {
            case "required":
                if (value == "" || value == null) {
                    results.push(name);
                }
                break;
            case "email":
                if (
                    !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(value)
                ) {
                    results.push(name);
                }
                break;
            case "number":
                if (!/^\d+$/.test(value)) {
                    results.push(name);
                }
                break;
            case "min":
                if (value.length < arg) {
                    results.push(name);
                }
                break;
            case "max":
                if (value.length > arg) {
                    results.push(name);
                }
                break;
            case "pattern":
                if (!new RegExp(arg).test(value)) {
                    results.push(name);
                }
                break;
            default:
                break;
        }
    });
    return results;
};

const FormGroup = function (controls) {
    const formControl = Object.fromEntries(
        Object.entries(controls).map(([key, value]) => {
            const [val, validate] = value;
            return [key, val ?? ""];
        })
    );
    return {
        ...formControl,
        disabled: false,
        value: function () {
            return Object.fromEntries(
                Object.entries(controls).map(([key, value]) => {
                    return [key, this[key] ?? ""];
                })
            );
        },
        valid: function () {
            return Object.entries(controls).every(
                ([key, value]) => !Validator(this[key], value[1])
            );
        },
        getErrors: function () {
            return Object.fromEntries(
                Object.entries(controls).map(([key, value]) => {
                    return [key, ValidatorWithError(this[key], value[1])];
                })
            );
        },
        disable: function () {
            this.disabled = true;
        },
        enable: function () {
            this.disabled = false;
        },
        patchValue: function (args) {
            Object.entries(args).map(([key, value]) => {
                this[key] = value;
            });
        },
        reset: function () {
            Object.entries(controls).map(([key, value]) => {
                this[key] = null;
            });
        },
        resetFilter: function () {
            Object.entries(controls).map(([key, value]) => {
                this[key] = "";
            });
        },
    };
};

export default FormGroup;
