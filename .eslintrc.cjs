module.exports = {
  root: true,
  extends: ["@nuxt/eslint-config", "prettier"],
  rules: {
    "sort-imports": [
      "error",
      {
        ignoreCase: false,
        ignoreDeclarationSort: true,
        ignoreMemberSort: false,
        memberSyntaxSortOrder: ["none", "all", "multiple", "single"],
        allowSeparatedGroups: false,
      },
    ],
    "vue/multi-word-component-names": "off",
  },
};
