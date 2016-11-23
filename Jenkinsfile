node('setekhmaseter') {
   stage('install tools') {

   git 'https://ebarboni@github.com/ebarboni/pourlahorde.git'

   sh "composer install"
}
stage('get data'){

sh 'sleep 0.5'
sh 'mkdir -p app/_data/'

sh "curl -o app/_data/guild.json 'https://eu.api.battle.net/wow/guild/Elune/Pour%20la%20horde?fields=achievements%2Cchallenge%2Cmembers%2Cnews&locale=fr_FR&apikey=${env.APIKEY}'"
def f = new File( pwd().'/app/_data', 'guild.json' )
def character = new groovy.json.JsonSlurper().parse(f);
}
stage ('build') {
   sh './vendor/bin/sculpin generate --env=prod'
}

}