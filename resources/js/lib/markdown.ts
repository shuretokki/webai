import MarkdownIt from "markdown-it";
import { createHighlighter } from "shiki";

const md = ref(new MarkdownIt({
  html: false,
  linkify: true,
  typographer: true,
}));

let isInit = false;
export function useMarkdown() {
  if (isInit)
    return md;

  isInit = true;

  createHighlighter({
    themes: ['vitesse-black'],
    langs: ['javascript', 'typescript', 'c++', 'php', 'python', 'html', 'css', 'json', 'bash', 'sql', 'vue', 'blade', 'go', 'rust', 'java', 'c', 'c#', 'dart', 'elixir', 'erlang', 'haskell', 'kotlin', 'lua', 'perl', 'r', 'ruby', 'scala', 'swift', 'zig'],
  }).then((highlighter) => {
    md.value = new MarkdownIt({
      html: false,
      linkify: true,
      typographer: true,
      highlight: (code, lang) => {
        const language = lang &&
          highlighter.getLoadedLanguages().includes(lang) ? lang : 'text';

        return highlighter.codeToHtml(code, { lang: language, theme: 'vitesse-black' });
      }
    });
  });

  return md;
}
