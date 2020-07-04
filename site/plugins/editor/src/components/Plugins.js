window.editor = {
  blocks: {},
  block(type, params) {
    const defaults = {
      type: type,
      icon: "page",
    };

    // extend the params with the defaults
    params = {
      ...defaults,
      ...params
    };

    // content editable options
    params.bind = {
      append: params.append,
      breaks: params.breaks,
      code: params.code,
      marks: params.marks,
      placeholder: params.placeholder,
    };

    this.blocks[type] = params;
  }
};
