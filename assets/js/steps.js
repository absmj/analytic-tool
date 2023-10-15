const steps = {
  get data() {
    return {
      current: 0,
    }
  },

  methods: {    
    test(x) {
      console.log(x)
    }
  },
}

const reports = new Proxy(steps, {

  get(target, name, receiever) {
      if(typeof target.methods[name] == 'function' && Reflect.has(target.methods, name)) {
        return target.methods[name]
      }
      else if(Reflect.has(target.data, name)) {
        return Reflect.get(target.data, name, receiever)
      }
  },

  set(target, name, value, receiever) {
    console.log(target.data[name], value)
    if(Reflect.has(target.data, name)) {
      return Reflect.set(target.data, name, value, receiever)
    }
  },
})

window.onload = function() {
    window.editor = CodeMirror.fromTextArea(document.getElementById('sql'), {
      mode: 'sql',
      indentWithTabs: true,
      smartIndent: true,
      lineNumbers: true,
      matchBrackets : true,
      autofocus: true,
      extraKeys: {"Ctrl-Space": "autocomplete"},
      hintOptions: {tables: {
        users: ["name", "score", "birthDate"],
        countries: ["name", "population", "size"]
      }}
    });

    reports.current = 6;
    reports.test(988)
  };
  