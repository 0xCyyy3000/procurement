<template>
    <div class="w-25 mb-3">
        <select class="form-select w-75 mt-2 p-2 text-white" style="background-color: var(--color-primary);"
            v-model="this.filterValue">
            <option :value=0>All</option>
            <option :value=1>Requisitions</option>
            <option :value=2>Purchased Orders</option>
        </select>
    </div>

    <section class="section-50">
        <div class="container">
            <div class="notification-ui_dd-content">
                <div class="notification-list" v-for="notification in filteredArray">
                    <div class="notification-list_content">
                        <div class="notification-list_detail">
                            <p>
                                <b>{{ notification.maker }}</b>
                                {{ notification.context }}
                            </p>

                            <p class="text-muted">
                                <span>Req no.{{ notification.reference }}</span>
                                <br />
                                <span>{{ notification.description }}</span>
                            </p>

                            <p class="text-muted">
                                <small>{{ notification.when }}</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import axios from "axios";

class Notifications {
    constructor(
        id,
        type,
        maker,
        maker_id,
        reference,
        status,
        context,
        message,
        description,
        when,
        evaluator
    ) {
        this.id = id;
        this.type = type;
        this.maker = maker;
        this.maker_id = maker_id;
        this.reference = reference;
        this.status = status;
        this.context = context;
        this.message = message;
        this.description = description;
        this.when = when;
        this.evaluator = evaluator;
    }
}

export default {
    data() {
        return {
            filterValue: 0,
            notifications: [],
        };
    },
    mounted() {
        // console.log("Ohayo!.");
    },
    methods: {
        sortArray: function () {
            this.notifications = this.notifications.sort((a, b) => b.id - a.id);
        }
    },
    computed: {
        filteredArray: function () {
            this.sortArray();
            if (this.filterValue == 0) {
                return this.notifications;
            } else if (this.filterValue == 1) {
                return this.notifications.filter(function (item) {
                    return item.type == 1;
                });
            } else if (this.filterValue == 2) {
                return this.notifications.filter(function (item) {
                    return item.type == 2;
                });
            }
        }
    },
    created() {
        axios.get("/api/test/notification/index").then(({ data }) => {
            data.contents.forEach((element) => {
                if (data.user === element.user_id) {
                    element.maker = 'You';
                }
                this.notifications.push(
                    new Notifications(
                        element.id,
                        element.type,
                        element.maker,
                        element.user_id,
                        element.reference,
                        element.status,
                        element.context,
                        element.message,
                        element.description,
                        element.updated_at,
                        element.evaluator
                    )
                );
            });
        });
    },
};
</script>
