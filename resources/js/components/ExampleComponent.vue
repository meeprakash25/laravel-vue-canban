<template>
    <div class="container mt-5">
        <div class="row mt-5">
            <div class="col-3" v-for="(column, colInd) in columns" :key="column.id">
                <div class="p-2 column">
                    <h3>{{ column.title }}</h3>
                    <button type="button" class="close" @click="deleteColumn(column.id, colInd)">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <hr>
                    <draggable class="draggable-list" group="my-group" v-bind="taskDragOptions" v-model="column.cards" @end="handleTaskMoved" :emptyInsertThreshold="50">
                        <div class="list-item" v-for="(card, cardInd) in column.cards" :key="card.id" @click="editCard(colInd, cardInd, card.id)">
                            <button type="button" class="close" @click="deleteCard(card.id, colInd, cardInd)">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            {{ card.title }}
                        </div>
                    </draggable>
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-primary" @click="addCard(column.id, colInd )" style="width:100%">Add Card</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <button class="btn btn-primary" @click="showAddColumnModal">Add Column</button>
            </div>
        </div>

        <modal name="add-column-modal" height="auto" @closed="addColumnModalClosed">
            <div class="card">
                <div class="card-header">
                    <h4 class="m-0">Add Column</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" placeholder="Enter title" v-model="columnTitle">
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-primary" @click="submitColumn">Submit</button>
                </div>
            </div>
        </modal>

        <modal name="add-card-modal" height="auto" @closed="addCardModalClosed">
            <div class="card">
                <div class="card-header">
                    <h4 class="m-0" v-text="cardModalTitle"></h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" placeholder="Enter title" v-model="cardTitle">
                    </div>
                    <div class="form-group">
                        <label for="title">Description</label>
                        <input type="text" class="form-control" id="title" placeholder="Enter description" v-model="cardDescription">
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-primary" @click="submitCard">Submit</button>
                </div>
            </div>
        </modal>
    </div>
</template>

<script>
import draggable from "vuedraggable";
import VModal from 'vue-js-modal'

Vue.use(VModal)

export default {
    data() {
        return {
            columns: [],
            value: [],
            columnTitle: '',
            cardId: '',
            cardTitle: '',
            cardDescription: '',
            cardModalTitle: '',
            cardIndex: '',
            cardColumnId: '',
            cardColumnIndex: '',
        };
    },
    methods: {
        showAddColumnModal() {
            this.$modal.show('add-column-modal');
        },
        hideAddColumnModal() {
            this.$modal.hide('add-column-modal');
        },
        addColumnModalClosed() {
            this.columnTitle = '';
        },
        hideAddCardModal() {
            this.$modal.hide('add-card-modal');
        },
        addCardModalClosed() {
            this.cardTitle = '';
            this.cardDescription = '';
            this.cardColumnId = '';
            this.cardColumnIndex = '';
            this.cardIndex = '';
            this.cardId = '';
            this.columnTitle = '';
        },
        addCard(colId, colIndex) {
            this.cardModalTitle = 'Add Card';
            this.cardColumnId = colId;
            this.cardColumnIndex = colIndex;
            this.$modal.show('add-card-modal');
        },
        editCard(columnIndex, cardIndex, cardId) {
            this.cardModalTitle = 'Edit Card';
            this.cardColumnIndex = columnIndex;
            this.cardIndex = cardIndex;
            this.cardId = cardId;
            this.cardTitle = this.columns[columnIndex].cards[cardIndex].title;
            this.cardDescription = this.columns[columnIndex].cards[cardIndex].description;
            this.$modal.show('add-card-modal');
        },
        getData() {
            this.columns = [];
            axios.get('column')
                .then(response => {
                    this.columns = response.data.columns
                })
        },
        handleTaskMoved(e) {
            axios.put("/column/sync", {columns: this.columns}).catch(err => {
                console.error(err.response.data.message);
            });
        },
        submitColumn(e) {
            e.preventDefault();
            axios.post("/column", {title: this.columnTitle})
                .then(response => {
                    if (response.data.status === 200) {
                        this.columns.push(response.data.column)
                        this.hideAddColumnModal();
                        console.log(response.data.message);
                    } else {
                        console.error(response.data.message);
                    }
                })
                .catch(err => {
                    console.error(err.response.data.message);
                });
        },
        submitCard(e) {
            e.preventDefault();
            if (this.cardModalTitle === 'Add Card') {
                var order = 1;
                if (this.columns[this.cardColumnIndex].cards !== undefined && this.columns[this.cardColumnIndex].cards.length) {
                    var lastCard = this.columns[this.cardColumnIndex].cards[this.columns[this.cardColumnIndex].cards.length - 1];
                    order = lastCard.order + 1;
                }
                axios.post("/card", {column_id: this.cardColumnId, title: this.cardTitle, description: this.cardDescription, order: order})
                    .then(response => {
                        if (response.data.status === 200) {
                            if (this.columns[this.cardColumnIndex].cards === undefined) {
                                this.columns[this.cardColumnIndex]['cards'] = [response.data.card];
                            } else {
                                this.columns[this.cardColumnIndex].cards.push(response.data.card);
                            }
                            this.hideAddCardModal();
                            console.log(response.data.message);
                        } else {
                            console.error(response.data.message);
                        }
                    })
                    .catch(err => {
                        console.error(err.response.data.message);
                    });
            } else {
                axios.put(`${'card/' + this.cardId}`, {id: this.cardId, title: this.cardTitle, description: this.cardDescription})
                    .then(response => {
                        if (response.data.status === 200) {
                            this.columns[this.cardColumnIndex].cards[this.cardIndex] = response.data.card;
                            this.hideAddCardModal();
                            console.log(response.data.message);
                        } else {
                            console.error(response.data.message);
                        }
                    })
                    .catch(err => {
                        console.error(err.response.data.message);
                    });
            }
        },
        deleteColumn(columnId, columnIndex) {
            axios.delete(`${'column/' + columnId}`)
                .then(response => {
                    if (response.data.status === 200) {
                        this.columns.splice(columnIndex, 1);
                        console.log(response.data.message);
                    } else {
                        console.error(response.data.message);
                    }
                })
                .catch(err => {
                    console.error(err.response.data.message);
                });
        },
        deleteCard(cardId, colIndex, cardIndex) {
            axios.delete(`${'card/' + cardId}`)
                .then(response => {
                    if (response.data.status === 200) {
                        this.columns[colIndex].cards.splice(cardIndex, 1);
                        console.log(response.data.message);
                    } else {
                        console.error(response.data.message);
                    }
                })
                .catch(err => {
                    console.error(err.response.data.message);
                });
        },
    },
    computed: {
        taskDragOptions() {
            return {
                animation: 200,
                group: "task-list",
            };
        }
    },
    mounted() {
        this.getData();
    }
};
</script>

<style lang="scss">
.column {
    min-height: 500px;
    color: #41464b;
    background-color: #e2e3e5;
    position: relative;
    padding: 1rem;
    margin-bottom: 1rem;
    border: 1px solid #d3d6d8;
    border-radius: 0.25rem;

    .close {
        cursor: pointer;
        padding: 5px 8px;
        margin: -35px 0px 0px auto;
        background-color: transparent;
        float: right;
        font-weight: 700;
        line-height: 1;
        color: #000;
        text-shadow: 0 1px 0 #fff;
        opacity: 0.5;
    }

    .draggable-list {
        background: #8eb53f;
        color: #fff;
        border: 1px solid;

        .close {
            cursor: pointer;
            padding: 0px 4px;
            margin: -35px -35px 0px auto;
            background-color: transparent;
            float: right;
            font-weight: 700;
            line-height: 1;
            color: #000;
            text-shadow: 0 1px 0 #fff;
            opacity: 0.5;
        }
    }

    .list-item {
        margin: 10px;
        padding: 40px;
        cursor: pointer;
        font-size: 18px;
        border-radius: 5px;
        background: #f4b836;
    }
}
</style>
