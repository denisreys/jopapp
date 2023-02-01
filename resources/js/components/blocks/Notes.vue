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
        The note will be save to press enter. 
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
          axios.post('/api/deletenote', { note_id: note_id })
            .then((r) => {
              console.log(r);
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

          axios.post('/api/savenote', {id: note_id, text: noteText})
            .then((r) =>{
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
        axios.get('/api/getnotes')
          .then((r) => {
            if(r.data) this.notes = r.data;
          }
        );
      }
    },
    mounted() {
      this.getNotes();
    }
  }
</script>
<style lang="scss">
  @import '../../../sass/_variables.scss';

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
      color: $black;
    }
  }
</style>
