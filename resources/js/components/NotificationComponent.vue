<template>
    <div class="w-25">
        <select class="form-select w-75 mt-2 p-2 text-white" style="background-color: var(--color-primary);"
            v-model="this.logOptions">
            <option value="0">All</option>
            <option value="1">Requisitions</option>
            <option value="2">Purchased Orders</option>
        </select>
    </div>

    <section class="section-50">
        <div class="container">
            <div class="notification-ui_dd-content">
                <div class="notification-list notification-list--unread" v-for="notification in this.notifications">
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

                <!-- <div class="notification-list">
                    <div class="notification-list_content">
                        <div class="notification-list_detail">
                            <p><b>Brian Cumin</b> reacted to your post</p>
                            <p class="text-muted">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Unde,
                                dolorem.</p>
                            <p class="text-muted"><small>10 mins ago</small></p>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </section>
</template>

<script>
import axios from "axios";

class Notifications {
    constructor(
        id,
        maker,
        reference,
        status,
        context,
        message,
        description,
        when,
        evaluator
    ) {
        this.id = id;
        this.maker = maker;
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
            logOptions: 0,
            notifications: [],
        };
    },
    mounted() {
        console.log("Ohayo!.");
    },
    watch: {
        logOptions(newLogOptions, oldLogOptions) {
            console.log(newLogOptions);
        }
    },
    created() {
        axios.get("/api/test/notification/index").then(({ data }) => {
            data.forEach((element) => {
                this.notifications.push(
                    new Notifications(
                        element.id,
                        element.maker,
                        element.requisition_id,
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
