<template>
  <div class="border border-solid border-slate-300 rounded-lg overflow-hidden bg-white shadow-sm">
    <!-- TOOLBAR BARIS 1: History, Format, Heading, Color -->
    <div v-if="editor" class="toolbar p-2 bg-slate-100 border-b border-slate-300 text-sm space-y-2">
      <div class="relative flex flex-wrap gap-1 items-center">
        <label class="text-slate-500 mr-3">Basic Formatting</label>
        <!-- Undo / Redo -->
        <button type="button" @click="undo" :disabled="!editor.can().undo()" class="btn">↶</button>
        <button type="button" @click="redo" :disabled="!editor.can().redo()" class="btn">↷</button>
        <div class="divider"></div>

        <!-- Headings & Paragraph -->
        <button type="button" @click="setHeading(1)" :class="{ 'active': editor.isActive('heading', { level: 1 }) }" class="btn font-bold">H1</button>
        <button type="button" @click="setHeading(2)" :class="{ 'active': editor.isActive('heading', { level: 2 }) }" class="btn font-bold">H2</button>
        <button type="button" @click="setHeading(3)" :class="{ 'active': editor.isActive('heading', { level: 3 }) }" class="btn font-bold">H3</button>
        <button type="button" @click="setParagraph" :class="{ 'active': editor.isActive('paragraph') }" class="btn">P</button>
        <div class="divider"></div>

        <!-- Basic Formatting -->
        <button type="button" @click="toggleBold" :class="{ 'active': editor.isActive('bold') }" class="btn font-bold">B</button>
        <button type="button" @click="toggleItalic" :class="{ 'active': editor.isActive('italic') }" class="btn italic">I</button>
        <button type="button" @click="toggleUnderline" :class="{ 'active': editor.isActive('underline') }" class="btn underline">U</button>
        <button type="button" @click="toggleStrike" :class="{ 'active': editor.isActive('strike') }" class="btn line-through">S</button>
        <button type="button" @click="toggleCode" :class="{ 'active': editor.isActive('code') }" class="btn font-mono">&lt;/&gt;</button>
        <div class="divider"></div>
        <!-- Media, Link & Reset -->
        <button type="button" @click="addLink" :class="{ 'active': editor.isActive('link') }" class="btn">🔗 Link</button>
        <button type="button" @click="addImage" class="btn">🖼️ Gambar</button>
      </div>

      <div class="flex flex-wrap gap-1 items-center">
        <label class="text-slate-500 mr-3">Text Formatting</label>
        <!-- Color Picker & Highlight -->
        <input type="color" @input="setColor" :value="currentColor" class="w-7 h-7 cursor-pointer border rounded self-center" title="Warna Teks" />
        <button type="button" @click="toggleHighlight" :class="{ 'active': editor.isActive('highlight') }" class="btn bg-yellow-200">🖍️</button>
        <div class="divider"></div>

        <!-- Alignment -->
        <button type="button" @click="setAlign('left')" :class="{ 'active': editor.isActive({ textAlign: 'left' }) }" class="btn">⬅️</button>
        <button type="button" @click="setAlign('center')" :class="{ 'active': editor.isActive({ textAlign: 'center' }) }" class="btn">↔️</button>
        <button type="button" @click="setAlign('right')" :class="{ 'active': editor.isActive({ textAlign: 'right' }) }" class="btn">➡️</button>
        <button type="button" @click="setAlign('justify')" :class="{ 'active': editor.isActive({ textAlign: 'justify' }) }" class="btn">☰</button>
        <div class="divider"></div>

        <!-- Lists -->
        <button type="button" @click="toggleBulletList" :class="{ 'active': editor.isActive('bulletList') }" class="btn">• Bullet</button>
        <button type="button" @click="toggleOrderedList" :class="{ 'active': editor.isActive('orderedList') }" class="btn">1. Numbering</button>
        <button type="button" @click="toggleTaskList" :class="{ 'active': editor.isActive('taskList') }" class="btn">☑ Checklist</button>
        <div class="divider"></div>
        <button type="button" @click="clearFormat" class="btn text-red-500" title="Bersihkan Formatting">🧹</button>
      </div>

        <!-- Table Management -->
      <div class="flex flex-wrap gap-1 items-center">
        <label class="text-slate-500 mr-3">Table Formatting</label>
        <button type="button" @click="insertTable" class="btn">📊 Tabel</button>
        <template v-if="editor.isActive('table')">
          <button type="button" @click="addColumn" class="btn">+ Kolom</button>
          <button type="button" @click="addRow" class="btn">+ Baris</button>
          <div class="divider"></div>
          <!-- 1. WARNA BACKGROUND SEL TABEL -->
          <div class="flex items-center gap-1 bg-white px-2 py-1 rounded border border-slate-200">
            <label class="text-xs font-semibold text-slate-600">BG Sel:</label>
            <input 
              type="color" 
              @input="setCellBackground" 
              :value="currentCellBg" 
              class="w-6 h-6 cursor-pointer border-0 rounded p-0" 
              title="Warna Latar Sel Tabel"
            />
          </div>

          <!-- 2. WARNA BORDER SEL TABEL -->
          <div class="flex items-center gap-1 bg-white px-2 py-1 rounded border border-slate-200">
            <label class="text-xs font-semibold text-slate-600">Border Sel:</label>
            <input 
              type="color" 
              @input="setCellBorderColor" 
              :value="currentCellBorder" 
              class="w-6 h-6 cursor-pointer border-0 rounded p-0" 
              title="Warna Garis Border Sel"
            />
          </div>

          <!-- 3. KETEBALAN BORDER TABEL -->
          <select @change="setCellBorderWidth" class="btn text-xs bg-white" :value="currentCellBorderWidth">
            <option value="1px">Border 1px</option>
            <option value="2px">Border 2px</option>
            <option value="4px">Border 4px</option>
            <option value="0px">Tanpa Border</option>
          </select>
          <div class="divider"></div>
          <button type="button" @click="deleteTable" class="btn text-red-600">Hapus Tabel</button>
        </template>
      </div>


      <div class="flex flex-wrap gap-1 items-center">
        <label class="text-slate-500 mr-3">Text Box Formatting</label>
        <!-- TOMBOL BUAT TEXT BOX BARU --> 
        <button type="button" @click="insertTextBox" class="btn bg-blue-50 text-blue-700 font-semibold border-blue-200">
          📦 Sisip Text Box
        </button>

        <!-- PANELS EDIT TEXT BOX (Hanya Muncul Saat Kursor di Dalam Text Box) -->
        <template v-if="editor.isActive('divWrapper')">
          <div class="divider" />
          <!-- WARNA BACKGROUND BOX -->
          <div class="flex items-center gap-1 bg-white px-2 py-1 rounded border border-slate-200">
            <label class="text-xs text-slate-600">BG Box:</label>
            <input 
              type="color" 
              @input="updateBoxBg" 
              :value="currentBoxBg" 
              class="w-6 h-6 cursor-pointer border-0 rounded p-0" 
              title="Warna Latar Text Box"
            />
          </div>

          <!-- WARNA BORDER BOX -->
          <div class="flex items-center gap-1 bg-white px-2 py-1 rounded border border-slate-200">
            <label class="text-xs text-slate-600">Border:</label>
            <input 
              type="color" 
              @input="updateBoxBorder" 
              :value="currentBoxBorder" 
              class="w-6 h-6 cursor-pointer border-0 rounded p-0" 
              title="Warna Garis Tepi Text Box"
            />
          </div>

          <select @change="updateBoxBorderWidth" class="btn text-xs bg-white"  :value="currentBoxBorderWidth" >
            <option value="1px">Border 1px</option>
            <option value="2px">Border 2px</option>
            <option value="4px">Border 4px</option>
            <option value="0px">Tanpa Border</option>
          </select>

          <!-- LEBAR PRESET (Opsional via Button) -->
          <button type="button" @click="setBoxWidth('50%')" class="btn text-xs">Width 50%</button>
          <button type="button" @click="setBoxWidth('100%')" class="btn text-xs">Width 100%</button>

          <!-- HAPUS TEXT BOX WRAPPER -->
          <div class="divider" />
          <button type="button" @click="unwrapBox" class="btn text-red-600 font-medium">
            🗑️ Un-wrap Box
          </button>
        </template>
      </div>
    </div>

    <!-- CANVAS CONTENT -->
    <editor-content :editor="editor" class="editor-content p-6 min-h-[400px]" />
  </div>
</template>

<script>
import { Editor, EditorContent, Extension, Node } from '@tiptap/vue-3'

// Extension Suite
import StarterKit from '@tiptap/starter-kit'
import Underline from '@tiptap/extension-underline'
import {TextStyle} from '@tiptap/extension-text-style'
import Color from '@tiptap/extension-color'
import Highlight from '@tiptap/extension-highlight'
import TextAlign from '@tiptap/extension-text-align'
import {Table} from '@tiptap/extension-table'
import {TableRow} from '@tiptap/extension-table-row'
import {TableCell} from '@tiptap/extension-table-cell'
import {TableHeader} from '@tiptap/extension-table-header'
import Image from '@tiptap/extension-image'
import Link from '@tiptap/extension-link'
import TaskList from '@tiptap/extension-task-list'
import TaskItem from '@tiptap/extension-task-item'

// 1. CUSTOM NODE UNTUK MEMPERTAHANKAN TAG <div> WRAPPER
const DivWrapper = Node.create({
  name: 'divWrapper',
  group: 'block',          // Dianggap sebagai blok HTML
  content: 'block+',       // Boleh berisi blok lain (paragraf, heading, tabel, dll)
  defining: true,

  addAttributes() {
    return {
      class: {
        default: null,
        parseHTML: element => element.getAttribute('class'),
        renderHTML: attributes => attributes.class ? { class: attributes.class } : {},
      },
      style: {
        default: null,
        parseHTML: element => element.getAttribute('style'),
        renderHTML: attributes => attributes.style ? { style: attributes.style } : {},
      },
      id: {
        default: null,
        parseHTML: element => element.getAttribute('id'),
        renderHTML: attributes => attributes.id ? { id: attributes.id } : {},
      },
      bgColor: {
        default: '#f8fafc',
        parseHTML: el => el.style.backgroundColor || '#f8fafc',
        // REFRESH REAL-TIME: Konversi langsung ke style HTML
        renderHTML: attrs => {
          return { style: `background-color: ${attrs.bgColor}` }
        },
      },
      borderColor: {
        default: 'white',
        parseHTML: el => el.style.borderColor || 'white',
        // REFRESH REAL-TIME: Konversi langsung ke style HTML
        renderHTML: attrs => {
          return { style: `border-color: ${attrs.borderColor}` }
        },
      },
      borderWidth: {
        default: '0px',
        parseHTML: el => el.style.borderWidth || '0px',
        // REFRESH REAL-TIME: Konversi langsung ke style HTML
        renderHTML: attrs => {
          return { style: `border-width: ${attrs.borderWidth}` }
        },
      },
      borderStyle: {
        default: 'solid',
        parseHTML: el => el.style.borderStyle || 'solid',
        renderHTML: attrs => {
          return { style: `border-style: ${attrs.borderStyle}` }
        },
      },
      width: {
        default: '100%',
        parseHTML: el => el.style.width || '100%',
        renderHTML: attrs => {
          return { style: `width: ${attrs.width}` }
        },
      },
    }
  },

  parseHTML() {
    return [
      { tag: 'div' }, // Mengenali tag <div>
    ]
  },

  renderHTML({ HTMLAttributes }) {
    // Tiptap akan menggabungkan atribut style dari renderHTML di addAttributes() secara otomatis
    return [
      'div',
      {
        'data-type': 'resizable-box',
        class: 'resizable-text-box',
        // Properti bawaan CSS box & resize
        style: 'border-width: 2px; padding: 16px; border-radius: 8px; margin: 16px 0; resize: both; overflow: auto; box-sizing: border-box;',
        ...HTMLAttributes
      },
      0
    ]
  },
})

// --- CUSTOM EXTENSION UNTUK MENJAGA ATRIBUT CLASS DAN STYLE ---
const KeepClassAndStyle = Extension.create({
  name: 'keepClassAndStyle',
  addGlobalAttributes() {
    return [
      {
        // Daftarkan semua tag HTML yang umum digunakan
        types: ['paragraph', 'heading', 'span', 'div', 'table','thead','tbody', 'tr', 'td', 'th', 'ul', 'ol', 'li','p'],
        attributes: {
          class: {
            default: null,
            parseHTML: element => element.getAttribute('class'),
            renderHTML: attributes => attributes.class ? { class: attributes.class } : {},
          },
          style: {
            default: null,
            parseHTML: element => element.getAttribute('style'),
            renderHTML: attributes => attributes.style ? { style: attributes.style } : {},
          },
        },
      },
    ]
  },
})
// CUSTOM TABLE ROW AGAR ATRIBUT CLASS DAN STYLE DI <tr> TIDAK HILANG
const CustomTableRow = TableRow.extend({
  addAttributes() {
    return {
      ...this.parent?.(),
      class: {
        default: null,
        parseHTML: element => element.getAttribute('class'),
        renderHTML: attributes => {
          if (!attributes.class) return {}
          return { class: attributes.class }
        },
      },
      style: {
        default: null,
        parseHTML: element => element.getAttribute('style'),
        renderHTML: attributes => {
          if (!attributes.style) return {}
          return { style: attributes.style }
        },
      },
    }
  },
})

// EXTENSION CUSTOM TABLE CELL DENGAN ATRIBUT WARNA & BORDER
const CustomTableCell = TableCell.extend({
  addAttributes() {
    return {
      ...this.parent?.(),
      // Atribut background-color sel
      backgroundColor: {
        default: null,
        parseHTML: el => el.style.backgroundColor || null,
        renderHTML: attrs => {
          if (!attrs.backgroundColor) return {}
          return { style: `background-color: ${attrs.backgroundColor}` }
        },
      },
      // Atribut border-color sel
      borderColor: {
        default: '#cbd5e1',
        parseHTML: el => el.style.borderColor || '#cbd5e1',
        renderHTML: attrs => {
          if (!attrs.borderColor) return {}
          return { style: `border-color: ${attrs.borderColor}` }
        },
      },
      // Atribut border-width sel
      borderWidth: {
        default: '1px',
        parseHTML: el => el.style.borderWidth || '1px',
        renderHTML: attrs => {
          if (!attrs.borderWidth) return {}
          return { style: `border-width: ${attrs.borderWidth}; border-style: solid;` }
        },
      },
    }
  },
})

export default {
  name: 'FullTiptapEditorOptions',
  
  components: {
    EditorContent,
  },

  props: {
    // Prop tunggal sesuai permintaan
    content: {
      type: String,
      default: ''
    }
  },

  emits: ['update:content'],

  data() {
    return {
      editor: null,
    }
  },

  computed: {
    currentColor() {
      return this.editor?.getAttributes('textStyle').color || '#000000'
    },
    currentBoxBg() {
      return this.editor?.getAttributes('divWrapper').bgColor || '#f8fafc'
    },
    currentBoxBorder() {
      return this.editor?.getAttributes('divWrapper').borderColor || '#cbd5e1'
    },
    currentBoxBorderWidth() {
      return this.editor?.getAttributes('divWrapper').borderWidth || '#cbd5e1'
    },
    currentCellBg() {
      return this.editor?.getAttributes('tableCell').backgroundColor || '#ffffff'
    },
    currentCellBorder() {
      return this.editor?.getAttributes('tableCell').borderColor || '#cbd5e1'
    },
    currentCellBorderWidth() {
      return this.editor?.getAttributes('tableCell').borderWidth || '#cbd5e1'
    }
  },

  watch: {
    // Memantau perubahan prop content dari induk
    content(newValue) {
      if (this.editor && this.editor.getHTML() !== newValue) {
        this.editor.commands.setContent(newValue, false)
      }
    }
  },

  mounted() {
    this.editor = new Editor({
      content: this.content,
      extensions: [
        DivWrapper,        // 👈 Tambahkan ini agar <div> pembungkus TIDAK HILANG
        KeepClassAndStyle,
        StarterKit,
        Underline,
        TextStyle,
        Color,
        Highlight.configure({ multicolor: true }),
        TextAlign.configure({ types: ['heading', 'paragraph'] }),
        Table.configure({ 
          resizable: true,
          allowTableNodeSelection: true, // 👈 Memungkinkan seleksi baris, kolom, dan multi-sel
        }), 
        TableHeader,
        CustomTableRow, // 👈 GUNAKAN CustomTableRow DI SINI (BUKAN TableRow BAWAAN)
        CustomTableCell, // Gunakan custom cell di sini
        Image,
        Link.configure({ openOnClick: false }),
        TaskList,
        TaskItem.configure({ nested: true }),
      ],
      onUpdate: () => {
        const html = this.editor.getHTML()
        this.$emit('update:content', html)
      },
    })
  },

  methods: {
    // History
    undo() { this.editor.chain().focus().undo().run() },
    redo() { this.editor.chain().focus().redo().run() },
    
    // DIV
    insertTextBox() {
      this.editor.chain().focus().wrapIn('divWrapper').run()
    },
    updateBoxBg(event) {
      this.editor.chain().focus().updateAttributes('divWrapper', {
        bgColor: event.target.value
      }).run()
    },
    updateBoxBorder(event) {
      this.editor.chain().focus().updateAttributes('divWrapper', {
        borderColor: event.target.value
      }).run()
    },
    updateBoxBorderWidth(event) {
      this.editor.chain().focus().updateAttributes('divWrapper', {
        borderWidth: event.target.value
      }).run()
    },
    setBoxWidth(widthValue) {
      this.editor.chain().focus().updateAttributes('divWrapper', {
        width: widthValue
      }).run()
    },
    unwrapBox() {
      this.editor.chain().focus().lift('divWrapper').run()
    },

    // Typography
    setHeading(level) { this.editor.chain().focus().toggleHeading({ level }).run() },
    setParagraph() { this.editor.chain().focus().setParagraph().run() },

    // Basic Formatting
    toggleBold() { this.editor.chain().focus().toggleBold().run() },
    toggleItalic() { this.editor.chain().focus().toggleItalic().run() },
    toggleUnderline() { this.editor.chain().focus().toggleUnderline().run() },
    toggleStrike() { this.editor.chain().focus().toggleStrike().run() },
    toggleCode() { this.editor.chain().focus().toggleCode().run() },
    clearFormat() { this.editor.chain().focus().unsetAllMarks().clearNodes().run() },

    // Color & Alignment
    setColor(event) { this.editor.chain().focus().setColor(event.target.value).run() },
    toggleHighlight() { this.editor.chain().focus().toggleHighlight({ color: '#fef08a' }).run() },
    setAlign(dir) { this.editor.chain().focus().setTextAlign(dir).run() },

    // Lists
    toggleBulletList() { this.editor.chain().focus().toggleBulletList().run() },
    toggleOrderedList() { this.editor.chain().focus().toggleOrderedList().run() },
    toggleTaskList() { this.editor.chain().focus().toggleTaskList().run() },

    // Table Actions
    insertTable() { this.editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run() },
    addColumn() { this.editor.chain().focus().addColumnAfter().run() },
    addRow() { this.editor.chain().focus().addRowAfter().run() },
    deleteTable() { this.editor.chain().focus().deleteTable().run() },
    // 1. Ubah Warna Background Sel (Bisa multi-cell/blok banyak sel sekaligus)
    setCellBackground(event) {
      this.editor.chain().focus().setCellAttribute('backgroundColor', event.target.value).run()
    },
    // 2. Ubah Warna Border Sel
    setCellBorderColor(event) {
      this.editor.chain().focus().setCellAttribute('borderColor', event.target.value).run()
    },
    // 3. Ubah Ketebalan Border Sel
    setCellBorderWidth(event) {
      this.editor.chain().focus().setCellAttribute('borderWidth', event.target.value).run()
    },
    // Media
    addLink() {
      const previousUrl = this.editor.getAttributes('link').href
      const url = window.prompt('Masukkan URL Link:', previousUrl)
      if (url === null) return
      if (url === '') {
        this.editor.chain().focus().extendMarkRange('link').unsetLink().run()
        return
      }
      this.editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run()
    },
    addImage() {
      const url = window.prompt('Masukkan URL Gambar:')
      if (url) {
        this.editor.chain().focus().setImage({ src: url }).run()
      }
    }
  },

  beforeUnmount() {
    if (this.editor) {
      this.editor.destroy()
    }
  }
}
</script>

<style scoped>
.btn {
  padding: 4px 8px;
  border-radius: 4px;
  background-color: #ffffff;
  border: 1px solid #cbd5e1;
  cursor: pointer;
  transition: all 0.15s ease-in-out;
}
.btn:hover { background-color: #f1f5f9; }
.btn.active {
  background-color: #3b82f6;
  color: #ffffff;
  border-color: #2563eb;
}
.btn:disabled { opacity: 0.4; cursor: not-allowed; }
.divider {
  width: 2px;
  background-color: rgb(184, 184, 184);
  margin: 0 5px;
  height: 20px;
}

:deep(.ProseMirror) {
  outline: none;
  min-height: 350px;
}
:deep(.ProseMirror h1) { font-size: 2rem; font-weight: 700; margin-bottom: 0.75rem; }
:deep(.ProseMirror h2) { font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem; }
:deep(.ProseMirror ul) { list-style-type: disc; padding-left: 1.5rem; }
:deep(.ProseMirror ol) { list-style-type: decimal; padding-left: 1.5rem; }

:deep(.ProseMirror table) {
  border-collapse: collapse;
  table-layout: fixed;
  width: 100%;
  margin: 1rem 0;
}
:deep(.ProseMirror td), :deep(.ProseMirror th) {
  border: 1px solid #cbd5e1;
  padding: 6px 10px;
  vertical-align: top;
}
:deep(.ProseMirror th) { background-color: #f8fafc; font-weight: bold; }

:deep(.ProseMirror ul[data-type="taskList"]) { list-style: none; padding: 0; }
:deep(.ProseMirror ul[data-type="taskList"] li) { display: flex; align-items: center; gap: 8px; }

/* Highlighting Sel yang Sedang Di-blok / Diseleksi (Multi-Cell) */
:deep(.ProseMirror .selectedCell) {
  position: relative;
  background-color: #dbeafe !important; /* Warna biru muda highlight */
}
/* Overlay Transparan Opsional agar Garis Border Sel Tetap Terlihat */
:deep(.ProseMirror .selectedCell::after) {
  z-index: 2;
  position: absolute;
  content: "";
  left: 0; 
  right: 0; 
  top: 0; 
  bottom: 0;
  background: rgba(59, 130, 246, 0.2); /* Biru transparan */
  pointer-events: none;
  border: 1px solid #3b82f6;
}
</style>