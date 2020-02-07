<template>
  <div class="easy-search__container">
    <svg
      v-on:click="hudIsVisible = !hudIsVisible"
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

    <div v-if="hudIsVisible" class="easy-search__hud-shade"></div>
    <div v-if="hudIsVisible" class="easy-search__hud">
      <div class="tip tip-top"></div>
      <form v-on:change="updateSearchInput" class="body">
        <div class="main-container">
          <div class="main">
            <h3 class="heading">Build your search query:</h3>
            <div v-for="item, i in input" class="input">
              <div class="field-row">
                <div class="input">
                  <div class="select fullwidth">
                    <select v-model="input[i].handle">
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
                  <div class="input">
                    <div class="select">
                      <select v-model="input[i].operator">
                        <option
                          v-if="!operator.needsSpecificField || input[i].handle !== '--any--'"
                          v-for="operator in availableOperators"
                          v-bind:value="operator.key"
                        >{{ operator.value }}</option>
                      </select>
                    </div>
                  </div>
                  <div v-if="getOperatorByKey(input[i].operator).showValueField" class="input input-grow">
                    <input type="text" v-model="item.value" class="text nicetext fullwidth" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      hudIsVisible: false,
      availableFields: [
        {
          handle: "--any--",
          label: "Any field"
        },
        {
          handle: "title",
          label: "Title"
        },
        {
          handle: "testField",
          label: "Test field"
        }
      ],
      availableOperators: [
        {
          key: "contains",
          value: "contains",
          fieldSearch: "{fieldHandle}:{value}",
          globalSearch: "{value}",
          showValueField: true,
          needsSpecificField: false
        },
        {
          key: "notContains",
          value: "does not contain",
          fieldSearch: "-{fieldHandle}:{value}",
          globalSearch: "-{value}",
          showValueField: true,
          needsSpecificField: false
        },
        {
          key: "equal",
          value: "is equal to",
          fieldSearch: "{fieldHandle}::{value}",
          globalSearch: '"{value}"',
          showValueField: true,
          needsSpecificField: false
        },
        {
          key: "notEqual",
          value: "is not equal to",
          fieldSearch: "-{fieldHandle}::{value}",
          globalSearch: '-"{value}"',
          showValueField: true,
          needsSpecificField: false
        },
        {
          key: "empty",
          value: "is empty",
          fieldSearch: "-{fieldHandle}:*",
          showValueField: false,
          needsSpecificField: true
        },
        {
          key: "notempty",
          value: "is not empty",
          fieldSearch: "{fieldHandle}:*",
          showValueField: false,
          needsSpecificField: true
        }
      ],
      input: [
        {
          handle: "--any--",
          operator: "contains",
          value: ""
        }
      ]
    };
  },
  mounted: function() {},
  methods: {
    updateSearchInput() {
      var searchQuery = this.buildSearchQuery();

      window.searchInput.value = searchQuery;
      Craft.elementIndex.searchText = searchQuery;
      Craft.elementIndex.updateElements();
    },
    buildSearchQuery() {
      var searchQuery = "";
      for (let i = 0; i < this.input.length; i++) {
        var row = this.input[i];

        if (i !== 0) {
          searchQuery += " ";
        }

        var operator = this.getOperatorByKey(row.operator);
        var searchString =
          row.handle == "--any--"
            ? operator.globalSearch
            : operator.fieldSearch;

        searchString = searchString.replace("{value}", row.value);
        searchString = searchString.replace("{fieldHandle}", row.handle);

        searchQuery += searchString;
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
    }
  }
};
</script>
