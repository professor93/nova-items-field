<template>
  <span v-if="field.indexCountItem">{{ field.value.length }}</span>
  <span v-else>
      <span v-for="(item, index) in field.value">
          {{ item }}&nbsp;
      </span>
  </span>
</template>

<script>
export default {
  props: ['resourceName', 'field'],
}
</script>
