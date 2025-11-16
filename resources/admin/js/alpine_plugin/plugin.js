import { gsap } from "gsap/all";
import * as _ from "lodash";

export default function (Alpine) {
    Alpine.store("animate", {
        enter: (target, fn) => {
            if (!target) return;
            if (target instanceof Element) {
                gsap.to(target.closest(".dialog"), {
                    autoAlpha: 1,
                    duration: 0.2,
                });
            }
            gsap.fromTo(
                target,
                { scale: 0.8, autoAlpha: 0 },
                { scale: 1, autoAlpha: 1, duration: 0.2 }
            ).eventCallback("onComplete", () => {
                fn ? fn(target) : null;
            });
        },
        leave: (target, fn) => {
            if (!target) return;
            gsap.to(target, {
                scale: 0.8,
                autoAlpha: 0,
                duration: 0.2,
            }).eventCallback("onComplete", () => {
                fn ? fn(target) : null;
            });
            if (target instanceof Element) {
                gsap.to(target.closest(".dialog"), {
                    autoAlpha: 0,
                    duration: 0.2,
                });
            }
        },
    });

    Alpine.store("libs", {
        getLastIndex: function (target) {
            return Math.max(
                ...Array.from(target ?? document.querySelectorAll("*"), (el) =>
                    parseFloat(window.getComputedStyle(el).zIndex)
                ).filter((zIndex) => !Number.isNaN(zIndex)),
                0
            );
        },
        playAnimateOnLoad: function (target, callback) {
            Alpine.store("animate").enter(target, (res) => {
                res?.removeAttribute("style");
                callback?.call(res);
            });
        },
    });

    Alpine.magic("json", () => (arg, encode = false) => {
        if (arg == null) return;
        if (arg == "") return;
        if (encode) {
            if (typeof arg !== "object") Error("Argument must be an object");
            return JSON.stringify(arg);
        }
        if (typeof arg !== "string") return Error("Argument must be a string");
        return JSON.parse(arg);
    });

    Alpine.magic("toJson", () => (arg) => {
        return JSON.stringify(arg);
    });

    Alpine.magic("isEmpty", () => (value) => {
        return _.isEmpty(value);
    });

    Alpine.magic("mask", () => (str, mask) => {
        let newStr = "";
        let strIndex = 0;
        for (let i = 0; i < mask?.length; i++) {
            if (mask[i] === "#") {
                newStr += str[strIndex] ?? "";
                strIndex++;
            } else {
                newStr += mask[i];
            }
        }
        return newStr;
    });

    Alpine.magic(
        "currency",
        () =>
            (number, symbol = null, separator = ".", fixed = 2) => {
                if (isNaN(number)) throw new Error(`${number} is not a number`);
                let str = parseFloat(number).toFixed(fixed);
                let parts = str.split(".");
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                if (symbol) {
                    parts[0] = symbol + parts[0];
                }
                return parts.join(separator);
            }
    );

    Alpine.magic("url", () => {
        const fullUrl = new URL(document.location);
        return {
            current: fullUrl,
            params: fullUrl.searchParams,
            hasParam: function (key, value) {
                if (key && value) {
                    return this.params.get(key) === value;
                }
                return this.params.get(key);
            },
            getParam: function (key) {
                return this.params.get(key) ?? null;
            },
        };
    });

    Alpine.magic("link", () => (url, type = "_self") => {
        window.open(url, type);
    });

    Alpine.directive(
        "render",
        (el, { value, expression }, { evaluate, evaluateLater }) => {
            const regex = /#\{(.*?)\}/g;
            let html = el.outerHTML;
            const text = Array.from(html.matchAll(regex), (m) => m[0]);
            text.map((item) => {
                const key = item.replace("#{", "").replace("}", "");
                const value = evaluate(key);
                html = html.replace(item, value);
            });
            el.outerHTML = html;
        }
    );

    Alpine.magic("http", () => (options) => {
        if (!options) return;
        Axios({
            url: options.url,
            method: options.method ?? "GET",
            data: options.data ?? {},
            params: options.params ?? {},
            headers: options.headers ?? {},
        })
            .then((res) => {
                if (typeof options.success !== "function") return;
                options.success(res);
            })
            .catch((err) => {
                if (typeof options.error !== "function") return;
                options.error(err);
            })
            .finally(() => {
                if (typeof options.finally !== "function") return;
                options.finally();
            });
    });
}
