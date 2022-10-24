<template>
  <PanelItem :index="index" :field="field">
      <template #value>
          <FormItemsTable
              v-if="theData.length > 0"
              :edit-mode="false"
              class="overflow-hidden"
          >
              <FormItemsHeader
                  :value-label="field.valueLabel"
              />

              <div
                  class="bg-gray-50 dark:bg-gray-700 overflow-hidden key-value-items"
              >
                  <FormItemsItem
                      v-for="(item, index) in theData"
                      :index="index"
                      :item="item"
                      :disabled="true"
                  />
              </div>
          </FormItemsTable>
      </template>
  </PanelItem>
</template>

<script>
import map from "lodash/map";
import FormItemsTable from './FormItemsTable'
import FormItemsHeader from './FormItemsHeader'
import FormItemsItem from './FormItemsItem'

export default {
  components: {FormItemsTable,FormItemsHeader,FormItemsItem},
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

    data: () => ({ theData: [] }),

    created() {
        this.theData = map(
            Object.entries(this.field.value || {}),
            ([key, value]) => ({
                value,
            })
        )
    },
}
</script>
