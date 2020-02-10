<template>
  <div class="easy-search__container">
    <svg
      v-on:click="toggleHud"
      class="easy-search__button"
      aria-hidden="true"
      focusable="false"
      data-prefix="fas"
      data-icon="sparkles"
      role="img"
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 512 512"
    >
      <path
        fill="currentColor"
        d="M324.42 103.15L384 128l24.84 59.58a8 8 0 0 0 14.32 0L448 128l59.58-24.85a8 8 0 0 0 0-14.31L448 64 423.16 4.42a8 8 0 0 0-14.32 0L384 64l-59.58 24.84a8 8 0 0 0 0 14.31zm183.16 305.69L448 384l-24.84-59.58a8 8 0 0 0-14.32 0L384 384l-59.58 24.84a8 8 0 0 0 0 14.32L384 448l24.84 59.58a8 8 0 0 0 14.32 0L448 448l59.58-24.84a8 8 0 0 0 0-14.32zM384 255.64a16.06 16.06 0 0 0-8.84-14.33l-112.57-56.39-56.28-112.77c-5.44-10.87-23.19-10.87-28.62 0l-56.28 112.77L8.84 241.31a16 16 0 0 0 0 28.67l112.57 56.39 56.28 112.77a16 16 0 0 0 28.62 0l56.28-112.77L375.16 270a16.07 16.07 0 0 0 8.84-14.36z"
      />
    </svg>

    <div v-if="hudIsVisible" class="easy-search__hud">
      <div class="tip tip-top"></div>
      <form class="body">
        <div class="main-container">
          <div class="main">
            <h3 class="heading">{{ getTranslated("Build a search query") }}:</h3>
            <div v-for="item, i in conditions" class="condition">
              <div v-if="i > 0" class="and-or-or">
                <div class="input">
                  <div class="select small">
                    <select v-on:change="updateSearchInput" v-model="item.andOrOr">
                      <option value=" ">{{ getTranslated('and') }}</option>
                      <option value=" OR ">{{ getTranslated('or') }}</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="field-row">
                <div class="input">
                  <div class="select fullwidth">
                    <select v-on:change="updateSearchInput" v-model="item.handle">
                      <option
                        v-for="field in availableFields"
                        v-bind:value="field.handle"
                      >{{ field.label }}</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="fieldrow">
                <div class="flex">
                  <div class="input flex-full">
                    <div class="select fullwidth">
                      <select v-on:change="updateSearchInput" v-model="item.operator">
                        <option
                          v-if="!operator.needsSpecificField || item.handle !== '--any--'"
                          v-for="operator in availableOperators"
                          v-bind:value="operator.key"
                        >{{ operator.value }}</option>
                      </select>
                    </div>
                  </div>
                  <div v-on:keyup="updateSearchInput" v-if="getOperatorByKey(item.operator).needsValueField" class="input input-important">
                    <input type="text" v-model="item.value" class="text nicetext fullwidth" />
                  </div>
                </div>
                <button v-if="conditionIsComplete(conditions.slice(-1)[0]) && i == conditions.length - 1" v-on:click="addConditionRow" class="add-btn">
                  &plus; {{ getTranslated('Add a condition') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
const defaultConditions = [
  {
    handle: "--any--",
    operator: "contains",
    value: ""
  }
];

export default {
  data() {
    return {
      currentElementType: null,
      currentSource: null,
      hudIsVisible: false,
      currentSearchQuery: '',
      availableFields: [],
      availableOperators: [
        {
          key: "contains",
          value: this.getTranslated('contains'),
          fieldSearch: "{fieldHandle}:{value}",
          globalSearch: "{value}",
          needsValueField: true,
          needsSpecificField: false
        },
        {
          key: "notContains",
          value: this.getTranslated('does not contain'),
          fieldSearch: "-{fieldHandle}:{value}",
          globalSearch: "-{value}",
          needsValueField: true,
          needsSpecificField: false
        },
        {
          key: "equal",
          value: this.getTranslated('is equal to'),
          fieldSearch: "{fieldHandle}::{value}",
          globalSearch: '"{value}"',
          needsValueField: true,
          needsSpecificField: false
        },
        {
          key: "notEqual",
          value: this.getTranslated('is not equal to'),
          fieldSearch: "-{fieldHandle}::{value}",
          globalSearch: '-"{value}"',
          needsValueField: true,
          needsSpecificField: false
        },
        {
          key: "empty",
          value: this.getTranslated('is empty'),
          fieldSearch: "-{fieldHandle}:*",
          needsValueField: false,
          needsSpecificField: true
        },
        {
          key: "notempty",
          value: this.getTranslated('is not empty'),
          fieldSearch: "{fieldHandle}:*",
          needsValueField: false,
          needsSpecificField: true
        }
      ],
      conditions: null,
    };
  },
  mounted: function() {
    var that = this;
    document.querySelector('body').addEventListener('click', function (event) {
      if (!event.target.closest('.easy-search__container') && !event.target.matches('.add-btn')) {
        that.toggleHud(false);
      }
    });
  },
  methods: {
    toggleHud(display = null) {
      // Set default conditions if none are set
      if (this.conditions === null) {
        this.conditions = defaultConditions;
      }

      // Set HUD visibility
      display = display !== null ? display : !this.hudIsVisible;
      this.hudIsVisible = display;

      if (display) {
        this.populateAvailableFields();
      }

      // Update search input to disabled/enable input field on HUD toggle
      this.updateSearchInput();
    },
    updateSearchInput() {
      var searchQuery = this.buildSearchQuery();

      if (searchQuery != this.currentSearchQuery) {
        window.searchInput.value = searchQuery;
        Craft.elementIndex.searchText = searchQuery;
        Craft.elementIndex.updateElements();
        this.currentSearchQuery = searchQuery;
      }

      if (searchQuery !== '' && this.hudIsVisible) {
        window.searchInput.disabled = true;
      } else {
        window.searchInput.disabled = false;
      }
    },
    buildSearchQuery() {
      var searchQuery = "";
      for (let i = 0; i < this.conditions.length; i++) {
        var condition = this.conditions[i];
        var operator = this.getOperatorByKey(condition.operator);

        if (this.conditionIsComplete(condition)) {
          if (searchQuery === "") {
            searchQuery += " ";
          } else {
            var andOrOr = condition.andOrOr !== undefined ? condition.andOrOr : '';
            searchQuery += andOrOr;
          }

          var searchString =
            condition.handle == "--any--"
              ? operator.globalSearch
              : operator.fieldSearch;

          searchString = searchString.replace("{value}", condition.value);
          searchString = searchString.replace("{fieldHandle}", condition.handle);

          searchQuery += searchString;
        }
      }

      return searchQuery;
    },
    getOperatorByKey(key) {
      for (let i = 0; i < this.availableOperators.length; i++) {
        var operator = this.availableOperators[i];

        if (operator.key == key) {
          return operator;
        }
      }

      return null;
    },
    getTranslated(string) {
      return Craft.t('easy-search', string);
    },
    addConditionRow() {
      this.conditions.push({
          andOrOr: ' ',
          handle: "--any--",
          operator: "contains",
          value: ""
        });
    },
    conditionIsComplete(condition) {
      var operator = this.getOperatorByKey(condition.operator);

      return !operator.needsValueField || condition.value !== '';
    },
    populateAvailableFields() {
      // First, get the element type and source
      var elementType = Craft.elementIndex !== undefined && Craft.elementIndex.elementType !== undefined ? Craft.elementIndex.elementType : null;
      var source = Craft.elementIndex.instanceState.selectedSource;

      if (elementType != this.currentElementType || source != this.currentSource) {       
        fetch('/actions/easy-search/default/get-available-fields?elementType=' + elementType + '&source=' + source)
          .then((response) => {
            return response.json();
          })
          .then((jsonResponse) => {
            this.availableFields = jsonResponse;
          });
      }

      this.currentElementType = elementType;
      this.currentSource = source;
    },
  }
};
</script>
