<template>
    <DefaultField
        :field="currentField"
        :errors="errors"
        :full-width-content="mode === 'modal'"
        :show-help-text="showHelpText"
    >
        <template #field>
            <FormItemsTable
                :can-delete-row="currentField.canDeleteRow"
            >
                <FormItemsHeader
                    :value-label="currentField.valueLabel"
                />

                <div class="bg-white dark:bg-gray-800 overflow-hidden key-value-items list-group">
                    <FormItemsItem
                        v-for="(item, index) in theData"
                        :index="index"
                        @remove-row="removeRow"
                        :item.sync="item"
                        :ref="item.id"
                        :read-only="!currentField.canEditRow"
                        :can-delete-row="currentField.canDeleteRow"
                    />
                </div>
            </FormItemsTable>

            <div
                class="mr-11"
                v-if="currentField.canAddRow && currentField.canEditRow"
            >
                <button
                    @click="addRowAndSelect"
                    :dusk="`${field.attribute}-add-item`"
                    type="button"
                    class="cursor-pointer focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 focus:ring-offset-4 dark:focus:ring-offset-gray-800 rounded-lg mx-auto text-primary-500 font-bold link-default mt-3 px-3 rounded-b-lg flex items-center"
                >
                    <Icon type="plus-circle" />
                    <span class="ml-1">{{ currentField.actionText }}</span>
                </button>
            </div>
        </template>
    </DefaultField>
</template>

<script>
import findIndex from 'lodash/findIndex'
import map from 'lodash/map'
import reject from 'lodash/reject'
import tap from 'lodash/tap'
import { FormField, HandlesValidationErrors } from 'laravel-nova'

import FormItemsTable from './FormItemsTable'
import FormItemsHeader from './FormItemsHeader'
import FormItemsItem from './FormItemsItem'

function guid() {
    var S4 = function () {
        return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1)
    }
    return (
        S4() +
        S4() +
        '-' +
        S4() +
        '-' +
        S4() +
        '-' +
        S4() +
        '-' +
        S4() +
        S4() +
        S4()
    )
}

export default {
    components: {FormItemsTable,FormItemsHeader,FormItemsItem},
    mixins: [HandlesValidationErrors, FormField],

    data: () => ({ theData: [] }),

    mounted() {
        this.populateValueData()
    },

    methods: {
        /*
         * Set the initial value for the field
         */
        populateValueData() {
            this.theData = map(Object.entries(this.value || {}), ([key, value]) => ({
                id: guid(),
                value,
            }))

            if (this.theData.length == 0) {
                this.addRow()
            }
        },

        /**
         * Provide a function that fills a passed FormData object with the
         * field's internal value attribute.
         */
        fill(formData) {
            formData.append(this.field.attribute, JSON.stringify(this.finalPayload))
        },

        /**
         * Add a row to the table.
         */
        addRow() {
            return tap(guid(), id => {
                this.theData = [...this.theData, { id, value: '' }]
                return id
            })
        },

        /**
         * Add a row to the table and select its first field.
         */
        addRowAndSelect() {
            return this.selectRow(this.addRow())
        },

        /**
         * Remove the row from the table.
         */
        removeRow(id) {
            return tap(
                findIndex(this.theData, row => row.id == id),
                index => this.theData.splice(index, 1)
            )
        },

        /**
         * Select the first field in a row with the given ref ID.
         */
        selectRow(refId) {
            return this.$nextTick(() => {
                this.$refs[refId][0].handleValueFieldFocus()
            })
        },

        onSyncedField() {
            this.populateValueData()
        },
    },

    computed: {
        /**
         * Return the final filtered json object
         */
        finalPayload() {
            return Object.assign({},
                reject(
                    map(this.theData, row => row && row.value ? row.value : undefined),
                    row => row === undefined
                )
            )
        },
    },
}
</script>
