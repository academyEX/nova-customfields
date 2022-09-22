import MadlibFormField from './components/madlib-field/FormField'
import MadlibDetailField from './components/madlib-field/DetailField'
import MultichoiceFormField from './components/multichoice-field/FormField'
import MultichoiceDetailField from './components/multichoice-field/DetailField'
import SubmissionMarkingFormField from './components/submission-marking-field/FormField'
import SubmissionMarkingDetailField from './components/submission-marking-field/DetailField'

Nova.booting((app, store) => {
  app.component('form-madlib-field', MadlibFormField)
  app.component('detail-madlib-field', MadlibDetailField)
  app.component('form-multichoice-field', MultichoiceFormField);
  app.component('detail-multichoice-field', MultichoiceDetailField);
  app.component('form-submission-marking-field', SubmissionMarkingFormField);
  app.component('detail-submission-marking-field', SubmissionMarkingDetailField);
})
