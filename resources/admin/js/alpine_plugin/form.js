export default function (Alpine) {
    Alpine.directive("form", (el, { value, expression }, { evaluate }) => {
        function submit(e) {
            const METHOD = el.getAttribute("method") || "post";
            const ACTION = el.getAttribute("action");
            e?.preventDefault();
            let response = {
                target: null,
                loading: false,
                error: false,
                data: null,
                status_code: null,
                completed: false,
                message: null,
            };
            const UNIQUE_ID = value ?? `form-${new Date().getTime()}`;
            el.setAttribute("unique-id", UNIQUE_ID);
            response.target = `[unique-id=${UNIQUE_ID}]`;
            const form = e.target;
            const data = new FormData(form);
            if (e.submitter instanceof HTMLButtonElement) {
                //add value button to data
                data.append(
                    e.submitter.getAttribute("name"),
                    e.submitter.getAttribute("value")
                );
            }
            response.loading = true;
            evaluation(evaluate, expression, response);
            Axios({
                method: METHOD,
                url: ACTION,
                data,
            })
                .then((res) => {
                    response.data = res.data;
                    response.status_code = res.status;
                    response.message = res.data.message;
                    response.error = false;
                })
                .catch((error) => {
                    response.data = error.response.data;
                    response.message = error.message;
                    response.status_code = error.response.status;
                    response.error = true;
                })
                .finally(() => {
                    response.loading = false;
                    response.completed = true;
                    evaluation(evaluate, expression, response);
                });
        }

        el.addEventListener("submit", (e) => {
            submit(e);
        });

        //disable enter key submit
        el.querySelectorAll("input").forEach((input) => {
            input.addEventListener("keydown", (e) => {
                if (e.keyCode === 13) {
                    e.preventDefault();
                }
            });
        });
    });
}

function evaluation(evaluate, expression, data) {
    return evaluate(`${expression}(${JSON.stringify(data)})`);
}
