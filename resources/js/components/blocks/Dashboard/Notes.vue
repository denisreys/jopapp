<template>
  <div class="sb__block sb__block--notes">
    <div class="sb__block__title">
      <span class="sb__block__title__span">Notes</span>
    </div>
    <div class="sb__add">
      <a href="#" class="sb__add__btn" @click.prevent="addNote()">
        <i class="fal fa-plus"></i>
      </a>
    </div>
    <template v-if="notes.length != 0">
      <input type="text" id="focusout" style="opacity:0">
      <ul class="sb__list" ref="notes">
          <li class="sb__list__item note" v-for="note in notes" :key="note.id">
            <div class="note__icon">-</div>
            <div class="note__actions">
              <a href="#" class="note__actions__a" @click.prevent="deleteNote(note.id)"><i class="fal fa-times"></i></a>
            </div>
            <div class="note__wrap">
              <div class="note__text" contenteditable @keydown.enter.prevent="event => saveNote(event, note.id)">
                {{note.text}}
              </div>
            </div>
          </li>
      </ul>
    </template>
    <template v-else>
      <div class="sb__desc">
        Press enter for save. 
      </div>
    </template>
  </div>
</template>

<script>
  import axios from 'axios';
  export default {
    data () {
      return {
        notes: [],
        wait: false
      }
    },
    methods: {
      addNote(){
        if(this.wait === false){
          this.notes.unshift({id: 0, text: ''});

          this.$nextTick(() => {
            this.$refs.notes.children[0].lastChild.lastChild.focus();
          });
        }
      },
      deleteNote(note_id){
        if(note_id){
          axios.post('/deletenote', { note_id: note_id })
            .then((r) => {
              this.getNotes();
            }
          );
        }
        else {
          this.notes.splice(0, 1)
        }
      },
      saveNote(e, note_id){
        var noteText = e.target.innerText;
        this.wait = true;

        if(noteText.length > 1){
          if(note_id){
            if(this.notes[this.notes.map(el => el.id).indexOf(note_id)].text === noteText)
              this.wait = false;
          }else note_id = 0;
          
          axios.post('/savenote', {id: note_id, text: noteText})
            .then((r) =>{
              document.querySelector('#focusout').focus();
              this.getNotes();
              this.wait = false;
            }
          );
        }
        else {
          if(note_id) this.deleteNote(note_id);
          else this.deleteNote();

          this.wait = false;
        }
      },
      getNotes(){
        axios.get('/getnotes')
          .then((r) => {
            this.$root.loading.loaded++;
            this.notes = r.data;
          }
        );
      }
    },
    created() {
      this.getNotes();
    }
  }
</script>
<style lang="scss">
  @import './resources/sass/_variables.scss';
  #focusout {
    opacity: 0;
    position: absolute;
  }
  .note {
    &:hover {
      .note__actions {
        display: block;
      }
      .note__icon {
        display: none;
      }
    }
  }
  .note__wrap {
    position: relative;
    margin-left: 20px;
  }
  .note__icon {
    float: left;
    font-size: 24px;
    line-height: 24px;
  }
  .note__text {
    display: inline-block;
    outline: none;
    border: none;
    min-width: 10px;

    br {
      display: none;
    }
  }
  .note__actions {
    display: none;
    float: left;
    line-height: 24px;

    .fal {
      color: $border;
      font-size: 16px;
    }
  }
  .note__actions__a {
    &:hover .fal {
      color: red;
    }
  }
</style>
