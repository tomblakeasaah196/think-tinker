-- ============================================================
--  Think & Tinker - blog seed (5 posts)
--
--  Pure ASCII on purpose: the mysql CLI defaults to latin1 on many
--  hosts, which mangles multi-byte characters on import. Once a row
--  holds invalid UTF-8, json_encode() returns false and the API
--  answers 500. Typography is preserved as HTML entities in `body`
--  (rendered as HTML); title/excerpt use plain ASCII because the
--  front end escapes them.
--
--  Safe to re-run. Author = user id 1 (Janeth Joseph).
-- ============================================================

SET NAMES utf8mb4;

DELETE FROM `blog_posts` WHERE `slug` IN ('why-your-child-fights-homework', 'choosing-a-primary-school-in-lagos', '12-kitchen-stem-experiments', 'raising-a-reader-before-age-10', 'screen-time-passive-vs-active');

INSERT INTO `blog_posts`
    (`title`, `slug`, `excerpt`, `body`, `featured_image`, `category`, `tags`, `author_id`, `status`, `published_at`)
VALUES
('Why Your Child Fights Homework Every Night (And What Actually Works)', 'why-your-child-fights-homework', 'The nightly homework battle is rarely about laziness. It is usually about one of four things  -  and once you know which one you are dealing with, the fix is often surprisingly small.', '<p>Ask any parent in Lagos what the hardest hour of the day is, and a striking number will say the same thing: the hour after dinner, when the books come out.</p>

<p>Here is what we have learned from sitting at those tables with hundreds of families: a child who fights homework is almost never simply lazy. Resistance is a signal. The trick is reading which signal you are getting.</p>

<h2>The four reasons children resist</h2>

<h3>1. The work is genuinely too hard</h3>
<p>This is the most common one, and the most commonly missed. A child who cannot do question one has no way to reach question ten, so avoiding the page entirely feels better than failing at it in front of you. Watch for a child who becomes vague &mdash; "I\'ll do it later," "the teacher didn\'t explain" &mdash; rather than openly defiant.</p>
<p><strong>What helps:</strong> sit down and do question one <em>with</em> them, out loud. If they can follow you but could not start alone, the gap is confidence, not ability. If they cannot follow you either, the gap is a concept from weeks ago that never landed, and no amount of pressure tonight will fix it.</p>

<h3>2. The work is far too easy</h3>
<p>Bright children get bored, and bored children stall. Twenty repetitions of something mastered in the first three feels like punishment. Watch for a child who does the first few items quickly and correctly, then drifts.</p>
<p><strong>What helps:</strong> agree a fair stopping point with the teacher. Many are happy for a confident child to do half the set well rather than all of it resentfully.</p>

<h3>3. They are exhausted</h3>
<p>Lagos traffic is a real academic variable. A child who left home at 6am and got back at 5pm has already worked a longer day than most adults. Their willpower is genuinely spent.</p>
<p><strong>What helps:</strong> a hard thirty minutes of nothing before books &mdash; food, rest, no screens. Counter-intuitive, but families who protect that half hour consistently report shorter homework sessions overall, not longer.</p>

<h3>4. Homework has become the whole relationship</h3>
<p>If the majority of what a child hears from you concerns school performance, resisting homework becomes the only available way to reclaim some ground. This one is painful to spot in yourself, and it is more common than anyone admits.</p>
<p><strong>What helps:</strong> deliberately spend fifteen minutes a day with your child where school is not mentioned at all. It sounds too simple to matter. It matters.</p>

<h2>Three things that work regardless of the cause</h2>

<p><strong>Same place, same time, every day.</strong> Decision-making is the expensive part. When the desk and the hour are fixed, the child stops negotiating and starts working, because there is nothing left to negotiate about.</p>

<p><strong>Short blocks with real breaks.</strong> Twenty-five minutes of work and five minutes off beats a shapeless ninety-minute slog every time. Younger children need shorter blocks still &mdash; for a six-year-old, fifteen and five is about right.</p>

<p><strong>Praise the effort, not the outcome.</strong> "You stayed with that even when it got hard" builds something durable. "You\'re so clever" quietly teaches a child that being stuck means they are no longer clever, so the safest move is to stop trying.</p>

<h2>When to bring in help</h2>

<p>If the same subject causes tears more than twice a week, or your child is falling behind despite genuinely trying, the problem is usually a specific missing foundation rather than attitude. That is fixable, but it needs diagnosis rather than more pressure.</p>

<p>Our home tutors start every engagement by finding where the gap actually begins &mdash; often two or three topics earlier than anyone expected. Once the foundation is repaired, the resistance tends to disappear on its own, because the work stops feeling impossible.</p>', 'assets/img/hero/tutoring-blocks.jpg', 'parenting_tips', '["homework", "routines", "motivation", "primary school"]', 1, 'published', '2026-06-14 09:00:00'),
('Choosing a Primary School in Lagos: 9 Questions Most Parents Never Ask', 'choosing-a-primary-school-in-lagos', 'School tours are designed to impress. These are the nine questions that tell you what a school is actually like on an ordinary Tuesday in the middle of term.', '<p>Every school tour shows you the same things: the computer lab, the swimming pool, the newest building, the wall of results. All useful. None of it tells you what your child\'s ordinary Tuesday will feel like.</p>

<p>After years of helping families through this decision, we have found that the answers that matter most come from questions schools are rarely asked. Here are nine worth writing down before you visit.</p>

<h2>About the teaching</h2>

<p><strong>1. How long has the average teacher been here?</strong><br>
High turnover is the single most reliable warning sign in Nigerian private education. Facilities can be financed; a settled, experienced staff cannot be bought quickly. If most teachers arrived within the last year, ask why &mdash; gently, but ask.</p>

<p><strong>2. What is the actual class size in Primary 3?</strong><br>
Not the advertised ratio, which often counts assistants and specialists. Ask about one specific year group, and ask to see that room while it is in use.</p>

<p><strong>3. What happens when a child falls behind?</strong><br>
Listen for a <em>process</em>: who notices, how quickly, what they do, how you are told. Schools that answer with a genuine system have one. Schools that answer with reassurance usually do not.</p>

<h2>About your child specifically</h2>

<p><strong>4. How do you handle a child who is well ahead of the class?</strong><br>
Extension is harder to deliver than remediation and is quietly neglected almost everywhere. A good answer is specific. A vague one means your bright child will spend a great deal of time waiting.</p>

<p><strong>5. What does a child who struggles with reading actually receive?</strong><br>
Ask how many sessions per week, delivered by whom, with what training. "We give extra attention" is not a programme.</p>

<p><strong>6. May I speak to two current parents you have not selected for me?</strong><br>
The reaction to this question is often more revealing than the answer. Confident schools say yes without much hesitation.</p>

<h2>About the practical realities</h2>

<p><strong>7. What is the total annual cost, including everything?</strong><br>
Uniform, books, excursions, exam fees, development levy, transport, the things that appear in term two. Ask for the full figure in writing. The gap between advertised fees and real cost can be substantial.</p>

<p><strong>8. What is the commute at 7am on a Monday?</strong><br>
Not the distance &mdash; the time, on the worst realistic morning. A daily ninety-minute crawl each way will shape your child\'s education more than almost any feature on the tour, and it compounds over years.</p>

<p><strong>9. What happens to your leavers?</strong><br>
Not the three star pupils in the brochure. Ask where the middle of last year\'s cohort went. That tells you what the school reliably produces, rather than what it is capable of at its very best.</p>

<h2>One thing to do that is not a question</h2>

<p>Ask to visit unannounced during an ordinary week, and watch the corridors between lessons. How do children speak to teachers? How do teachers speak to children who are not performing? Are the youngest ones relaxed?</p>

<p>Ten minutes of that will tell you more than the entire prospectus. Fit matters more than prestige &mdash; the best school in Lagos is not the best school for every child in Lagos, and a confident child in a good-enough school will almost always outperform an anxious child in a famous one.</p>', 'assets/img/hero/founder-portrait.jpg', 'educational_insights', '["school selection", "admissions", "Lagos", "consulting"]', 1, 'published', '2026-06-28 09:00:00'),
('12 STEM Experiments You Can Run Tonight With Things Already in Your Kitchen', '12-kitchen-stem-experiments', 'No kit, no shopping, no screen. Twelve real experiments using what is already in your kitchen  -  plus the one question that turns any of them into actual science.', '<p>You do not need a laboratory to raise a scientist. Nearly every principle a primary school child needs to grasp can be demonstrated with what is already in your kitchen tonight.</p>

<p>Before the list, the most important part.</p>

<h2>The question that makes it science</h2>

<p>Ask <strong>"what do you think will happen, and why?"</strong> &mdash; <em>before</em> you begin. Then let them be wrong.</p>

<p>Without a prediction, an experiment is just a trick they watched. With one, it becomes a hypothesis they tested, and being wrong becomes interesting instead of embarrassing. That single habit is the whole difference between entertainment and science.</p>

<h2>Ages 4&ndash;7</h2>

<p><strong>1. Sink or float.</strong> A bowl of water and ten objects. Predict each one first. The surprises &mdash; a heavy candle floating, a small coin sinking &mdash; are the point.</p>

<p><strong>2. Dancing rice.</strong> Rice in fizzy water. Bubbles attach, lift the grains, pop at the surface, and the grains fall. Density made visible.</p>

<p><strong>3. Walking water.</strong> Two glasses, one with coloured water, joined by a folded paper towel. Water climbs the towel and travels across. Capillary action &mdash; the same way plants drink.</p>

<p><strong>4. Salt on ice.</strong> Two ice cubes, salt on one. It melts faster. Ask why roads in cold countries are salted.</p>

<h2>Ages 8&ndash;11</h2>

<p><strong>5. Baking soda volcano &mdash; done properly.</strong> Everyone knows it. Make it science by varying <em>one</em> thing: measure the vinegar carefully and see whether double the vinegar gives double the eruption. It does not, and working out why is the lesson.</p>

<p><strong>6. Egg in vinegar.</strong> Leave a raw egg submerged for 48 hours. The shell dissolves and leaves a translucent, bouncy membrane. Then discuss tooth enamel and fizzy drinks.</p>

<p><strong>7. Invisible ink.</strong> Write with lemon juice, warm the paper carefully near a lamp. Oxidation, and a genuinely good afternoon.</p>

<p><strong>8. Homemade lava lamp.</strong> Oil, water, food colouring, half an effervescent tablet. Density and immiscibility, and it is beautiful.</p>

<p><strong>9. Rubber chicken bone.</strong> A clean chicken bone in vinegar for three days goes rubbery as the calcium leaches out. A memorable argument for eating well.</p>

<h2>Ages 12&ndash;14</h2>

<p><strong>10. Extract DNA from a banana.</strong> Mash banana, add salty water, a little washing-up liquid, then cold rubbing alcohol poured gently down the side. White stringy DNA gathers at the boundary. Real genetics, on a kitchen table.</p>

<p><strong>11. Build a water filter.</strong> A cut bottle layered with cotton, sand, gravel and charcoal. Pour muddy water through. Discuss why the output is clearer but still not safe to drink &mdash; an important distinction, and a good introduction to public health engineering.</p>

<p><strong>12. Measure reaction time.</strong> One person drops a ruler between another\'s fingers; the distance it falls converts to time. Repeat thirty times, plot the results, then test whether tiredness or noise changes them. This is a complete experiment: variable, control, data, conclusion.</p>

<h2>Afterwards</h2>

<p>Ask three questions: What surprised you? What would you change if we did it again? Where else have you seen this happen?</p>

<p>That last one is where science stops being a school subject and starts being a way of looking at the world.</p>

<p>Our STEM &amp; Reading Club runs experiments like these every Saturday, with the equipment, the mess and the packing-up handled by someone else.</p>', 'assets/img/hero/stem-multiplication.jpg', 'stem_activities', '["STEM", "experiments", "home activities", "ages 4-14"]', 1, 'published', '2026-07-12 09:00:00'),
('Raising a Reader: How the Habit Is Really Built Before Age 10', 'raising-a-reader-before-age-10', 'Children who read for pleasure do better across every subject, not just English. The habit is built far earlier than most parents realise  -  and rarely by the methods they try first.', '<p>Reading for pleasure is the closest thing education has to a cheat code. Children who do it perform better in mathematics and science too, and the advantage widens as they get older.</p>

<p>Which makes it worth understanding how the habit is actually formed &mdash; because the two most common approaches, rewards and requirements, tend to produce children who can read but choose not to.</p>

<h2>Ages 1&ndash;3: the sound of it</h2>

<p>Long before meaning, children are learning that language is pleasurable. Rhythm, repetition and silly voices matter more than plot. Read the same book forty times if that is what they want; the repetition is the learning.</p>
<p>Ten minutes a day at this age does more than an hour a week later on.</p>

<h2>Ages 4&ndash;6: the mechanics &mdash; and the trap</h2>

<p>This is where children learn to decode, and where many quietly begin to dislike reading.</p>
<p>The trap is making <em>every</em> encounter with a book a test. If the only time your child sees print is when they are being assessed on it, print becomes a place where they get things wrong.</p>
<p><strong>Keep two separate activities.</strong> One is reading practice, where they read to you and you correct gently. The other is story time, where you read to them and nothing is asked of them at all. Do not let the second turn into the first. Continue reading aloud to your child long after they can read alone &mdash; well into secondary school if they will let you.</p>

<h2>Ages 7&ndash;9: letting go of taste</h2>

<p>This is where the habit is won or lost, and where parents most often get in the way with the best of intentions.</p>
<p>Comics count. Football magazines count. The same book six times counts. A book two years "below their level" counts. Volume and enjoyment build fluency; difficulty does not, not yet.</p>
<p>A child who reads twenty easy books they love will end up a stronger reader than one who is marched through four worthy ones they resent. Let them choose badly. Choosing at all is the skill being learned.</p>

<h2>Ages 10+: protecting the time</h2>

<p>By now the competition is real &mdash; homework, lessons, screens, friends. Reading survives only if it has protected space.</p>
<p>The most effective intervention we see is also the least sophisticated: twenty minutes before lights out, no screens in the room, a lamp and a book. It works because it is not a reward or a punishment. It is simply what happens at that time, the way brushing teeth is.</p>

<h2>Four things that quietly kill it</h2>

<ul>
<li><strong>Paying for pages.</strong> Rewards work briefly, then reading becomes work that has stopped paying.</li>
<li><strong>Reading as punishment.</strong> "Go and read your book" said in anger does lasting damage.</li>
<li><strong>Comprehension questions after every chapter.</strong> Save those for schoolwork.</li>
<li><strong>Never being seen to read.</strong> Children copy what adults do, not what adults recommend.</li>
</ul>

<h2>If your child says they hate reading</h2>

<p>They almost never mean it literally. They usually mean one of three things: it is too hard and they are hiding that, they have never found a book about something they actually care about, or reading has become associated with being corrected.</p>

<p>All three are solvable. The first needs a quiet assessment of decoding, not more practice. The second needs a librarian\'s patience and a willingness to abandon your own taste entirely. The third needs a few months where nobody corrects them at all.</p>', 'assets/img/hero/why-flashcards.jpg', 'reading_guides', '["reading", "literacy", "habits", "early years"]', 1, 'published', '2026-07-26 09:00:00'),
('Screen Time Isn\'t the Enemy  -  Passive Screen Time Is', 'screen-time-passive-vs-active', 'Counting hours is the wrong measurement. What a child does during those hours predicts far more than how many there were  -  and it changes what you need to police.', '<p>Most screen time advice reduces to a number: two hours, one hour, none before bed. It is easy to remember and not very useful, because it treats a child editing a video and a child watching autoplay clips as though they are doing the same thing.</p>

<p>They are not. A more useful question than <em>how long</em> is <em>what kind</em>.</p>

<h2>Three kinds of screen time</h2>

<p><strong>Passive.</strong> Content arrives; the child receives it. Autoplaying videos, endless feeds, background television. Requires nothing, ends only when someone intervenes. This is the kind worth limiting hard.</p>

<p><strong>Interactive.</strong> The child makes decisions the screen responds to. Most games, quiz apps, puzzles. Genuinely mixed &mdash; a well-designed strategy game asks real things of a child; a slot-machine-shaped mobile game does not.</p>

<p><strong>Creative.</strong> The child makes something that did not exist before. Drawing, animating, coding, editing, composing, building. The screen is a tool rather than a destination. There is little evidence that this needs the same tight limits as the first kind, and treating it as equivalent teaches children that making things and consuming things are the same activity.</p>

<h2>The practical rule</h2>

<p>Rather than a total budget, try a ratio. In our experience the families with the least conflict about screens use something close to <strong>one part passive to two parts interactive or creative</strong> &mdash; and they enforce it by making the good options easy rather than by policing the bad ones.</p>

<p>Coding on the laptop is available any time. YouTube requires asking. The friction does most of the work.</p>

<h2>The three rules that matter more than the count</h2>

<p><strong>1. No screens in the bedroom overnight.</strong> If you enforce one thing, make it this. Sleep loss undermines everything else you are trying to build, and no negotiation about content matters if the child is exhausted.</p>

<p><strong>2. No screens during meals &mdash; including yours.</strong> This one only works if it applies to the whole household, which is exactly why it is effective.</p>

<p><strong>3. Watch or play together sometimes.</strong> Ask what they like about it. You will learn more in ten minutes of genuine curiosity than in a month of monitoring, and it keeps the conversation open for the years when you will need it.</p>

<h2>The signal to watch for</h2>

<p>The reliable warning sign is not hours. It is what gets displaced.</p>

<p>A child who still sleeps well, still sees friends, still moves their body, still reads something occasionally and still tolerates boredom is probably fine, even on a number that would alarm you. A child who has quietly stopped doing those things needs intervention regardless of how modest the total looks.</p>

<p><strong>Boredom in particular is worth protecting.</strong> It is where imagination starts. A child who has never been bored has never had to invent anything, and the ability to sit with an empty half hour and make something out of it is a skill &mdash; one that a permanently available screen removes the need to ever develop.</p>', 'assets/img/hero/moment-3.jpg', 'parenting_tips', '["screen time", "digital", "balance", "modern parenting"]', 1, 'published', '2026-08-09 09:00:00');
