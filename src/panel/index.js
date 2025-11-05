import SerpPreview from "./components/SerpPreview.vue";
import "./index.css";

window.panel.plugin("smallsystems/vuek-seo", {
  fields: {
    "seo-preview": SerpPreview,
  },
});
