<script setup>
import { computed, ref, useApi, useContent, usePanel } from "kirbyuse";
import { onMounted } from "vue";

const panel = usePanel();
const api = useApi();

const props = defineProps({
  label: String,
  faviconUrl: String,
  siteTitle: String,
  siteUrl: String,
  titleSeparator: String,
  titleContentKey: String,
  width: String,
  defaultTitle: String,
  descriptionContentKey: String,
  defaultDescription: String,
  searchConsoleUrl: String,
  config: Object,
  metaImage: [String, Object],
});

// Local state
const previewUrl = ref("");
onMounted(async () => {
  const data = await api.get(panel.view.path, { select: "previewUrl" });
  previewUrl.value = data.previewUrl;
});

const { currentContent } = useContent();

const path = computed(() => {
  if (!previewUrl.value) return "";
  const url = new URL(previewUrl.value);
  return url.pathname;
});

const title = computed(() => {
  let template = panel.view.props.blueprint;
  // If there is a title content key, use it
  if (currentContent.value[props.titleContentKey]) {
    // If the template is home, use the site title and the title content key
    if (template === "home" || template === "site") {
      return [
        props.siteTitle,
        props.titleSeparator,
        currentContent.value[props.titleContentKey],
      ].join(" ");
    } else {
      return [
        currentContent.value[props.titleContentKey],
        props.titleSeparator,
        props.siteTitle,
      ].join(" ");
    }
  } else {
    // If there is no title content key, use the default title
    return (
      props.defaultTitle ||
      [panel.view.title, props.titleSeparator, props.siteTitle].join(" ")
    );
  }
});

const description = computed(() => {
  if (currentContent.value[props.descriptionContentKey]) {
    return currentContent.value[props.descriptionContentKey];
  } else {
    return props.defaultDescription;
  }
});

const image = computed(() => {
  return props.metaImage;
});
</script>

<template>
  <k-field :label="label" class="k-serp-section">
    <div
      class="ksp-overflow-hidden ksp-rounded-[var(--input-rounded)] ksp-bg-[var(--input-color-back)] ksp-p-4"
    >
      <div class="ksp-mb-2 ksp-flex ksp-items-center ksp-gap-3">
        <figure
          v-if="faviconUrl"
          class="ksp-inline-flex ksp-aspect-square ksp-h-[26px] ksp-w-[26px] ksp-items-center ksp-justify-center ksp-rounded-full ksp-border ksp-border-solid ksp-border-[var(--serp-favicon-border)] ksp-bg-[var(--serp-favicon-background)]"
        >
          <img
            class="ksp-block ksp-h-[18px] ksp-w-[18px]"
            :src="faviconUrl"
            alt=""
          />
        </figure>
        <div class="ksp-flex ksp-flex-col">
          <span class="ksp-text-sm ksp-text-[var(--serp-color-text)]">{{
            siteTitle
          }}</span>
          <span
            class="ksp-line-clamp-1 ksp-text-xs ksp-text-[var(--serp-color-text)]"
            >{{ siteUrl }}{{ path }}</span
          >
        </div>
      </div>

      <h3
        class="ksp-line-clamp-1 ksp-text-xl ksp-text-[var(--serp-color-title)]"
      >
        {{ title }}
      </h3>

      <p
        v-show="description"
        class="ksp-mt-1 ksp-line-clamp-2 ksp-text-sm ksp-text-[var(--serp-color-text)]"
      >
        {{ description }}
      </p>

      <div v-if="image" class="ksp-mt-3"><img :src="image" alt="" /></div>
    </div>

    <k-button-group v-show="searchConsoleUrl" class="ksp-mt-2 ksp-w-full">
      <k-button :link="searchConsoleUrl" icon="open" target="_blank">
        Google Search Console
      </k-button>
    </k-button-group>
  </k-field>
</template>

<style scoped>
.k-serp-section {
  --serp-favicon-background: light-dark(#f1f3f4, #fff);
  --serp-favicon-border: light-dark(#ecedef, #5c5f5e);
  --serp-color-title: light-dark(#1a0dab, #99c3ff);
  --serp-color-text: light-dark(#4d5156, #bfbfbf);
}
</style>
