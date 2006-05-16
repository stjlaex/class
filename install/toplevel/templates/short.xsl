<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
<!-- xslt for short subject reports presented in a single table split across two page 
	columns with with one to several assessment grades but without a written 
	comment; ideal for single page-->

<xsl:output method='html' />

<xsl:template match='text()' />

<xsl:template match="html/body/div">
  <xsl:apply-templates select="student"/>
</xsl:template>

<xsl:template match="student">
<div id='logo'>
<img src="../images/schoollogo.png" width="130px"/>
</div>

<div class='studenthead'>
	<table id='studentdata'>
	  <tr>
		<td>
		  <label>
			Student
		  </label>
		  <xsl:value-of select="forename/value/text()" />&#160;
		  <xsl:value-of select="middlenames/value/text()" />&#160;
		  <xsl:value-of select="surname/value/text()" />
		</td>
	  </tr>
	  <tr>		
		<td>
		  <label>
			Form
		  </label>
		  <xsl:value-of select="registrationgroup/value/text()" />
		  <xsl:text>&#160;&#160;&#160;&#160;&#160;&#160;</xsl:text>
		  <label>Date</label>
		  <xsl:value-of select="reports/publishdate/text()" />
		</td>
	  </tr>
	</table>
</div>

<div class='spacer'></div>
  <table class="descriptor" id="left">
	<tr><td></td><td>Attainment / Resultados</td></tr>
	<tr><td>A</td><td>Excellent</td></tr>
	<tr><td>B+</td><td>Very Good</td></tr>
	<tr><td>B</td><td>Good</td></tr>
	<tr><td>C</td><td>Satisfactory</td></tr>
	<tr><td>D</td><td>Unsatisfactory</td></tr>
  </table>
  <table class="descriptor">
	<tr><td>Effort / Esfuerzo</td><td></td></tr>
	<tr><td>Sobrasaliente</td><td>1</td></tr>
	<tr><td>Notable</td><td>2</td></tr>
	<tr><td>Bien</td><td>3</td></tr>
	<tr><td>Suficente</td><td>4</td></tr>
	<tr><td>Insuficiente</td><td>5</td></tr>
  </table>

<div class='spacer'></div>

<xsl:apply-templates select="reports"/>

<div class='spacer'></div>

<div id='footer'>
  <div class='comment'><label>Tutor Comment:</label></div>
  <div class='spacer'></div>
  <div class='signature'><label>Tutor:</label></div>
</div>

  <hr />
</xsl:template>

<xsl:template match="reports">
  <table class="grades" id="gradesleft">
	<tr>
	  <th>
		<xsl:text>Subject</xsl:text>
	  </th>
	  <xsl:call-template name="assheader" />
	</tr>
	<xsl:apply-templates select="report[(position() mod 2)=1]">
	  <xsl:sort select="course/value/text()" order="descending" case-order="upper-first" />
	</xsl:apply-templates>
  </table>

  <table class="grades" id="gradesright">
	<tr>
	  <th>
		<xsl:text>Subject</xsl:text>
	  </th>
	  <xsl:call-template name="assheader" />
	</tr>
	<xsl:apply-templates select="report[(position() mod 2)=0]">
	  <xsl:sort select="course/value/text()" order="descending" case-order="upper-first" />
	</xsl:apply-templates>
  </table>
</xsl:template>

<xsl:template match="report">
<tr>
  <th>
	<xsl:choose>
	  <xsl:when test="component/value/text()!=' '">
		<xsl:value-of select="component/value/text()" />
	  </xsl:when>
	  <xsl:otherwise>
		<xsl:value-of select="subject/value/text()" />
	  </xsl:otherwise>
	</xsl:choose>
  </th>

  <xsl:call-template name="asscell" />

</tr>
</xsl:template>


<xsl:template name="assheader">
  <xsl:param name="index">1</xsl:param>
  <xsl:variable name="maxindex">
	<xsl:value-of select="count(asstable/ass/label)+1"/>
  </xsl:variable>
  <xsl:variable name='asslabel' select='asstable/ass/label' />
  <xsl:if test="$index &lt; $maxindex">
	<th>
		  <label>
	  <xsl:value-of select="$asslabel[$index]" />  
	  <xsl:text>&#160; </xsl:text>
		  </label>
	</th>
    <xsl:call-template name="assheader">
	  <xsl:with-param name="index" select="$index + 1"/>
    </xsl:call-template>
  </xsl:if>
</xsl:template>

<xsl:template name="asscell">
  <xsl:param name="index">1</xsl:param>
  <xsl:variable name="maxindex">
	<xsl:value-of select="count(../asstable/ass/label)+1"/>
  </xsl:variable>
  <xsl:variable name='asslabel' select='../asstable/ass/label' />

  <xsl:if test="$index &lt; $maxindex">
	<td>
	  <xsl:value-of select="assessments/assessment[printlabel/value=$asslabel[$index]]/result/value/text()" />  
	  <xsl:text>&#160; </xsl:text>
	</td>
    <xsl:call-template name="asscell">
	  <xsl:with-param name="index" select="$index + 1"/>
    </xsl:call-template>
  </xsl:if>
</xsl:template>

</xsl:stylesheet>
